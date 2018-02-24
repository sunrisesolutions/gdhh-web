<?php

namespace App\Admin\HoSo\PhanBo;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TruongPhuTrachDoiAdminController extends BaseCRUDAdminController {
	
	public function nopBangDiemAction($id = null, $hocKy, Request $request) {
		if( ! in_array($hocKy, [ 1, 2 ])) {
			throw new \InvalidArgumentException();
		}
		
		/**
		 * @var PhanBo $phanBo
		 */
		$phanBo = $this->admin->getSubject();
		if( ! $phanBo) {
			throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
		}
		
		/** @var TruongPhuTrachDoiAdmin $admin */
		$admin = $this->admin;
		
		$chiDoan = $phanBo->getChiDoan();
		
		$admin->setAction('nop-bang-diem');
		$admin->setActionParams([
			'chiDoan'        => $chiDoan,
			'christianNames' => ThanhVien::$christianNames,
			'hocKy'          => $hocKy
		]);
		
		if( ! $admin->isGranted('NOP_BANG_DIEM', $phanBo)) {
			throw new AccessDeniedHttpException();
		}
		
		if($phanBo->coTheNopBangDiem($hocKy)) {
			$hoanTatBangDiemHKMethod = 'hoanTatBangDiemHK' . $hocKy;
			$phanBo->$hoanTatBangDiemHKMethod();
			$this->admin->getModelManager()->update($phanBo);
		}
		
		return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_truongphutrachdoi_nhapDiemThieuNhi', [ 'id' => $id ]));
	}
	
	public function dongQuyAction($id = null, Request $request) {
		/**
		 * @var PhanBo $phanBo
		 */
		$phanBo = $this->admin->getSubject();
		if( ! $phanBo) {
			throw new NotFoundHttpException(sprintf('unable to find the PhanBo with id : %s', $id));
		}
		
		/** @var TruongPhuTrachDoiAdmin $admin */
		$admin   = $this->admin;
		
		$manager = $this->get('doctrine.orm.default_entity_manager');
		
		if($request->isMethod('post')) {
			$diem     = floatval($request->request->get('diem', 0));
			$soTien   = $request->request->getInt('soTien', 0);
			$dongQuy  = $request->request->getBoolean('dongQuy', false);
			$ngheoKho = $request->request->getBoolean('ngheoKho', false);
			
			$phanBoId = $request->request->get('phanBoId');
			$phanBo   = $this->getDoctrine()->getRepository(PhanBo::class)->find($phanBoId);
			
			if(($dongQuy === false && $soTien === 0 || $dongQuy === true && $soTien > 0 || ! empty($phanBo))) {
				
				$phanBo->setTienQuyDong($soTien);
				$phanBo->setDaDongQuy($dongQuy);
				$phanBo->setNgheoKho($ngheoKho);
				$tv = $phanBo->getThanhVien();
				$tv->setNgheoKho($ngheoKho);
				
				$manager->persist($phanBo);
				$manager->persist($tv);
				$manager->flush();

//
				return new JsonResponse([ 'OK' ], 200);
			} else {
				return new JsonResponse([ 404, 'Không thể tìm thấy Thiếu-nhi này' ], 404);
			}
		}
		
		$phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
		$manager->persist($phanBo);
		$manager->flush();
		
		
		$phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
		
		$admin->namHoc = $phanBo->getNamHoc();
		$admin->setAction('dong-quy');
		$admin->setActionParams([
			'chiDoan'        => $phanBo->getChiDoan(),
			'phanBoHangNam'  => $phanBoHangNam,
			'christianNames' => ThanhVien::$christianNames
		]);
		
		return parent::listAction();
	}
	
	public function nhapDiemThieuNhiAction($id = null, Request $request) {
		/**
		 * @var PhanBo $phanBo
		 */
		$phanBo = $this->admin->getSubject();
		if( ! $phanBo) {
			throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
		}
		
		/** @var PhanBoAdmin $admin */
		$admin = $this->admin;
		
		$manager = $this->get('doctrine.orm.default_entity_manager');
		/** @var User $user */
		$user      = $this->getUser();
		$thanhVien = $user->getThanhVien();;
		if(empty($_phanBo = $thanhVien->getPhanBoNamNay())) {
			throw new NotFoundHttpException('No Group Assignment found');
		}
		
		if($_phanBo->getId() !== $phanBo->getId()) {
			if( ! $_phanBo->quanLy($phanBo)) {
				throw new UnauthorizedHttpException('not authorised');
			}
		}
		
		$chiDoan = $phanBo->getChiDoan();
		$hocKy   = 1;
		if($chiDoan->isDuocDuyetBangDiemHK1()) {
			$hocKy = 2;
		}
		
		$readOnly = false;
		if(($phanBo->isHoanTatBangDiemHK1() && $hocKy === 1) || ($phanBo->isHoanTatBangDiemHK2() && $hocKy === 2)) {
			$readOnly = true;
		}
		
		$cotDiemHeaders     = [];
		$cotDiemAttrs       = [];
		$cotDiemLabels      = [];
		$cotDiemCellFormats = [];
		if($hocKy === 1) {
			$cotDiemHeaders['cc9']               = 'CC-9';
			$cotDiemHeaders['cc10']              = 'CC-10';
			$cotDiemHeaders['cc11']              = 'CC-11';
			$cotDiemHeaders['cc12']              = 'CC-12';
			$cotDiemHeaders['tbCCTerm1']         = 'TB. CC';
			$cotDiemHeaders['quizTerm1']         = 'TB. Miệng';
			$cotDiemHeaders['midTerm1']          = 'Điểm 1 Tiết';
			$cotDiemHeaders['finalTerm1']        = 'Thi HK1';
			$cotDiemHeaders['tbTerm1']           = 'TB. HK1';
			$cotDiemHeaders['sundayTicketTerm1'] = 'Phiếu lễ CN';
			
			$cotDiemAttrs['cc9']               = 'cc9';
			$cotDiemAttrs['cc10']              = 'cc10';
			$cotDiemAttrs['cc11']              = 'cc11';
			$cotDiemAttrs['cc12']              = 'cc12';
			$cotDiemAttrs['tbCCTerm1']         = 'tbCCTerm1';
			$cotDiemAttrs['quizTerm1']         = 'quizTerm1';
			$cotDiemAttrs['midTerm1']          = 'midTerm1';
			$cotDiemAttrs['finalTerm1']        = 'finalTerm1';
			$cotDiemAttrs['tbTerm1']           = 'tbTerm1';
			$cotDiemAttrs['sundayTicketTerm1'] = 'sundayTicketTerm1';
			
			$cotDiemLabels['cc9']               = 'điểm Chuyên-cần tháng 9';
			$cotDiemLabels['cc10']              = 'điểm Chuyên-cần tháng 10';
			$cotDiemLabels['cc11']              = 'điểm Chuyên-cần tháng 11';
			$cotDiemLabels['cc12']              = 'điểm Chuyên-cần tháng 12';
			$cotDiemLabels['tbCCTerm1']         = 'điểm Trung-bình Chuyên-cần';
			$cotDiemLabels['quizTerm1']         = 'điểm Trung-bình Miệng';
			$cotDiemLabels['midTerm1']          = 'điểm 1 Tiết/Giữa-kỳ';
			$cotDiemLabels['finalTerm1']        = 'điểm Thi Cuối-kỳ';
			$cotDiemLabels['tbTerm1']           = 'điểm Trung-bình Học-kỳ 1';
			$cotDiemLabels['sundayTicketTerm1'] = 'phiếu lễ Chúa-nhật';
			
			$cotDiemCellFormats ['cc9']               = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc10']              = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc11']              = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc12']              = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbCCTerm1']         = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['quizTerm1']         = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['midTerm1']          = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['finalTerm1']        = "type:'numeric', format: '0,0.00'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbTerm1']           = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['sundayTicketTerm1'] = "type:'numeric'";
		} elseif($hocKy === 2) {
			$cotDiemHeaders['cc1']               = 'CC-1';
			$cotDiemHeaders['cc2']               = 'CC-2';
			$cotDiemHeaders['cc3']               = 'CC-3';
			$cotDiemHeaders['cc4']               = 'CC-4';
			$cotDiemHeaders['cc5']               = 'CC-5';
			$cotDiemHeaders['tbCCTerm2']         = 'TB. CC';
			$cotDiemHeaders['quizTerm2']         = 'TB. Miệng';
			$cotDiemHeaders['midTerm2']          = 'Điểm 1 Tiết';
			$cotDiemHeaders['finalTerm2']        = 'Thi HK2';
			$cotDiemHeaders['tbTerm2']           = 'TB. HK2';
			$cotDiemHeaders['tbTerm1']           = 'TB. HK1';
			$cotDiemHeaders['tbYear']            = 'TB. NĂM';
			$cotDiemHeaders['sundayTicketTerm2'] = 'Phiếu CN';
			$cotDiemHeaders['sundayTickets']     = 'Phiếu Cả năm';
			
			$cotDiemAttrs['cc1']        = 'cc1';
			$cotDiemAttrs['cc2']        = 'cc2';
			$cotDiemAttrs['cc3']        = 'cc3';
			$cotDiemAttrs['cc4']        = 'cc4';
			$cotDiemAttrs['cc5']        = 'cc5';
			$cotDiemAttrs['tbCCTerm2']  = 'tbCCTerm2';
			$cotDiemAttrs['quizTerm2']  = 'quizTerm2';
			$cotDiemAttrs['midTerm2']   = 'midTerm2';
			$cotDiemAttrs['finalTerm2'] = 'finalTerm2';
			$cotDiemAttrs['tbTerm2']    = 'tbTerm2';
			
			$cotDiemAttrs['tbTerm1']           = 'tbTerm1';
			$cotDiemAttrs['tbYear']            = 'tbYear';
			$cotDiemAttrs['sundayTicketTerm2'] = 'sundayTicketTerm2';
			$cotDiemAttrs['sundayTickets']     = 'sundayTickets';
			
			
			$cotDiemLabels['cc1']        = 'điểm Chuyên-cần tháng 1';
			$cotDiemLabels['cc2']        = 'điểm Chuyên-cần tháng 2';
			$cotDiemLabels['cc3']        = 'điểm Chuyên-cần tháng 3';
			$cotDiemLabels['cc4']        = 'điểm Chuyên-cần tháng 4';
			$cotDiemLabels['cc5']        = 'điểm Chuyên-cần tháng 5';
			$cotDiemLabels['tbCCTerm2']  = 'điểm Trung-bình Chuyên-cần';
			$cotDiemLabels['quizTerm2']  = 'điểm Trung-bình Miệng';
			$cotDiemLabels['midTerm2']   = 'điểm 1 Tiết/Giữa-kỳ';
			$cotDiemLabels['finalTerm2'] = 'điểm Thi Cuối-kỳ';
			$cotDiemLabels['tbTerm2']    = 'điểm Trung-bình Học-kỳ 2';
			$cotDiemLabels['tbTerm1']    = 'TB. HK1';
			
			$cotDiemLabels['tbYear']            = 'TB. NĂM';
			$cotDiemLabels['sundayTicketTerm2'] = 'phiếu lễ Chúa-nhật';
			$cotDiemLabels['sundayTickets']     = 'phiếu lễ CN Cả Năm';
			
			$cotDiemCellFormats ['cc1']        = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc2']        = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc3']        = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc4']        = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc5']        = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbCCTerm2']  = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['quizTerm2']  = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['midTerm2']   = "type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['finalTerm2'] = "type:'numeric', format: '0,0.00'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbTerm2']    = "type:'numeric',readOnly:true, format: '0,0.00'";
			
			$cotDiemCellFormats ['tbTerm1'] = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats['tbYear']   = "type:'numeric',readOnly:true, format: '0,0.00'";
			
			$cotDiemCellFormats ['sundayTicketTerm2'] = "type:'numeric'";
			$cotDiemCellFormats ['sundayTickets']     = "type:'numeric', readOnly:true";
		}
		
		$cacCotDiemBiLoaiBo = $chiDoan->getCotDiemBiLoaiBo();
		if(is_array($cacCotDiemBiLoaiBo)) {
			foreach($cacCotDiemBiLoaiBo as $cotDiemBiLoaiBo) {
				unset($cotDiemHeaders[ $cotDiemBiLoaiBo ], $cotDiemAttrs[ $cotDiemBiLoaiBo ], $cotDiemCellFormats[ $cotDiemBiLoaiBo ], $cotDiemLabels[ $cotDiemBiLoaiBo ]);
			}
		}
		if($request->isMethod('post')) {
			$diem    = floatval($request->request->get('diem', 0));
			$cotDiem = $request->request->getAlnum('cotDiem');
			if( ! in_array($cotDiem, array_values($cotDiemAttrs))) {
				return new JsonResponse([ 415, 'Không hỗ trợ Cột-điểm này' ], 415);
			}
			
			$phanBoId = $request->request->get('phanBoId');
			$phanBo   = $this->getDoctrine()->getRepository(PhanBo::class)->find($phanBoId);
			
			if( ! ($diem === null || empty($phanBo))) {
				if($readOnly) {
					throw new AccessDeniedHttpException();
				}
				
				$bangDiem = $phanBo->getBangDiem();
				$setter   = 'set' . ucfirst($cotDiem);
				$bangDiem->$setter($diem);
				$manager = $this->get('doctrine.orm.default_entity_manager');
				
				if(substr($cotDiem, 0, 2) === 'cc') {
					$bangDiem->tinhDiemChuyenCan($hocKy);
					$bangDiem->tinhDiemHocKy($hocKy);
				} elseif(substr($cotDiem, 0, 6)) {
					$bangDiem->tinhDiemHocKy($hocKy);
				}
				
				$tbCC = 0;
				if($hocKy === 1) {
					$tbCC          = $bangDiem->getTbCCTerm1();
					$tbTerm        = $bangDiem->getTbTerm1();
					$tbYear        = 0;
					$sundayTickets = $bangDiem->getSundayTickets();
					$category      = '';
					$retention     = '';
					$awarded       = '';
				} elseif($hocKy === 2) {
					$tbCC          = $bangDiem->getTbCCTerm2();
					$tbTerm        = $bangDiem->getTbTerm2();
					$tbYear        = $bangDiem->getTbYear();
					$sundayTickets = $bangDiem->getSundayTickets();
					$category      = $bangDiem->getCategory();
					$retention     = $bangDiem->isGradeRetention();
					$awarded       = $bangDiem->isAwarded();
				}
				
				$manager->persist($bangDiem);
//				$manager->persist($phanBo);
//				$manager->persist($chiDoan);
				$manager->flush();

//
				return new JsonResponse([
					'tbCC'          => $tbCC,
					'tbTerm'        => $tbTerm,
					'tbYear'        => $tbYear,
					'sundayTickets' => $sundayTickets
				], 200);
			} else {
				return new JsonResponse([ 404, 'Không thể tìm thấy Thiếu-nhi này' ], 404);
			}
		}
		
		$phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
		$manager->persist($phanBo);
		$manager->flush();
		
		$admin->setAction('nhap-diem-thieu-nhi');
		$admin->setActionParams([
			'chiDoan'        => $phanBo->getChiDoan(),
			'phanBo'         => $phanBo,
			'phanBoHangNam'  => $phanBoHangNam,
			'hocKy'          => $hocKy,
			'cotDiemHeaders' => $cotDiemHeaders,
			'cotDiemAttrs'   => $cotDiemAttrs,
			'cotDiemLabels'  => $cotDiemLabels,
			
			'cotDiemCellFormats' => $cotDiemCellFormats,
			'christianNames'     => ThanhVien::$christianNames
		]);
		
		return parent::listAction();
	}
	
	public function thieuNhiNhomDownloadBangDiemAction($id = null, $hocKy, Request $request) {
		if( ! in_array($hocKy, [ 1, 2 ])) {
			throw new InvalidArgumentException();
		}
		
		/**
		 * @var PhanBo $phanBo
		 */
		$phanBo = $this->admin->getSubject();
		if( ! $phanBo) {
			throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
		}
		
		/** @var PhanBoAdmin $admin */
		$admin = $this->admin;
		
		//		\PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		$hocKy = intval($hocKy);
		
		$thanhVienService = $this->get('app.binhle_thieunhi_thanhvien');
		
		$filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
//		$response = new BinaryFileResponse($zipFile);
//		$response->headers->set('Content-Disposition', 'attachment;filename=' . str_replace(' ', '-', 'ihp_export_' . $dateAlnum . '.zip'));
		$phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
		
		$phpExcelObject->getProperties()->setCreator("Solution")
		               ->setLastModifiedBy("Solution")
		               ->setTitle("Download - Raw Data")
		               ->setSubject("Bang Diem HK1")
		               ->setDescription("Raw Data")
		               ->setKeywords("office 2005 openxml php")
		               ->setCategory("Raw Data Download");
		
		$phpExcelObject->setActiveSheetIndex(0);
		$activeSheet = $phpExcelObject->getActiveSheet();
		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$phpExcelObject->setActiveSheetIndex(0);
		
		$sWriter = new SpreadsheetWriter($activeSheet);
		$thanhVienService->writeBangDiemDoiNhomGiaoLyHeading($sWriter, $hocKy, $phanBo);
		
		if($hocKy === 1) {
			foreach(range('A', 'N') as $columnID) {
				$activeSheet->getColumnDimension($columnID)
				            ->setAutoSize(true);
			}
		}
		
		if($hocKy === 1) {
			$thanhVienService->writeBangDiemDoiNhomGiaoLyHK1Data($sWriter, $phanBo);
		}

//		$colDimensions = $activeSheet->getColumnDimensions();
//		foreach($colDimensions as $dimension) {
//			$dimension->setAutoSize(true);
//		}
		
		$activeSheet->calculateColumnWidths();
		// create the writer
		$writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
		// create the response
		$response = $this->get('phpexcel')->createStreamedResponse($writer);
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		
		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
		
		return $response;
	}
	
}