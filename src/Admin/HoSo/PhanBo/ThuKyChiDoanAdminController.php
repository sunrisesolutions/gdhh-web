<?php

namespace App\Admin\HoSo\PhanBo;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use App\Helper\BangDiemHandsonTableService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ThuKyChiDoanAdminController extends BaseCRUDAdminController {
	
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
		
		/** @var ThuKyChiDoanAdmin $admin */
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
		$thuKy = $phanBo->getThanhVien()->getThuKyChiDoanObj();
		
		if($thuKy->coTheNopBangDiem($hocKy)) {
			$hoanTatBangDiemHKMethod = 'hoanTatBangDiemHK' . $hocKy;
			$chiDoan->$hoanTatBangDiemHKMethod(true);
			$manager = $this->getDoctrine()->getManager();
			$manager->persist($chiDoan);
			$manager->flush();
		}
		
		return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_thukychidoan_nhapDiemThieuNhi', [ 'id' => $id ]));
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
			if( ! $_phanBo->getThanhVien()->getThuKyChiDoanObj()->isThieuNhiCungChiDoan($phanBo)) {
				throw new UnauthorizedHttpException('not authorised');
			}
		}
		
		$cotDiemHeaders     = [];
		$cotDiemAttrs       = [];
		$cotDiemLabels      = [];
		$cotDiemCellFormats = [];
		$bangDiemHelper     = $this->get(BangDiemHandsonTableService::class);
		$result             = $bangDiemHelper->prepareTable($phanBo, $cotDiemHeaders, $cotDiemAttrs, $cotDiemLabels, $cotDiemCellFormats);
		
		$readOnly = $result ['readOnly'];
		$hocKy    = $result['hocKy'];
		
		if($request->isMethod('post')) {
			return $bangDiemHelper->ghiDiem($request, $cotDiemHeaders, $cotDiemAttrs, $cotDiemLabels, $cotDiemCellFormats, $result);
		}
		
		
		$phanBoHangNam = $phanBo->getThanhVien()->getThuKyChiDoanObj()->getCacPhanBoThieuNhiPhuTrach();
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
			'christianNames'     => ThanhVien::$christianNames,
			'nopDiemUrl'         =>
				$this->get('router')->generate('admin_app_hoso_phanbo_thukychidoan_nopBangDiem',
					[
						'id'    => $phanBo->getId(),
						'hocKy' => $hocKy
					]
				)
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