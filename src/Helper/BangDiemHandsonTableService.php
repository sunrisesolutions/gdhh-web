<?php

namespace App\Helper;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\PhanBo;
use App\Service\BaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BangDiemHandsonTableService extends BaseService {
	
	public function prepareTable(PhanBo $phanBo, &$cotDiemHeaders, &$cotDiemAttrs, &$cotDiemLabels, &$cotDiemCellFormats) {
		$chiDoan  = $phanBo->getChiDoan();
		$hocKy    = $chiDoan->getHocKyHienTai();
		$readOnly = $phanBo->getThanhVien()->getHuynhTruongObj()->isBangDiemReadOnly();
		if($hocKy === 1) {
			$cotDiemHeaders['cc9']               = 'CC-9';
			$cotDiemHeaders['cc10']              = 'CC-10';
			$cotDiemHeaders['cc11']              = 'CC-11';
			$cotDiemHeaders['cc12']              = 'CC-12';
			$cotDiemHeaders['tbCCTerm1']         = 'TB. CC';
			$cotDiemHeaders['quizTerm1']         = 'TB. Miệng';
			$cotDiemHeaders['midTerm1']          = 'Điểm 1 Tiết';
			$cotDiemHeaders['finalTerm1']        = 'Thi HK1';
			$cotDiemHeaders['tbGLTerm1']         = 'TB. Giáo Lý';
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
			$cotDiemAttrs['tbGLTerm1']         = 'tbGLTerm1';
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
			$cotDiemLabels['tbGLTerm1']         = 'TB. Giáo Lý HK1';
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
			$cotDiemCellFormats ['tbGLTerm1']         = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['tbTerm1']           = "type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['sundayTicketTerm1'] = "type:'numeric'";
		} elseif($hocKy === 2) {
			$cotDiemHeaders['cc1']        = 'CC-1';
			$cotDiemHeaders['cc2']        = 'CC-2';
			$cotDiemHeaders['cc3']        = 'CC-3';
			$cotDiemHeaders['cc4']        = 'CC-4';
			$cotDiemHeaders['cc5']        = 'CC-5';
			$cotDiemHeaders['tbCCTerm2']  = 'TB. CC';
			$cotDiemHeaders['quizTerm2']  = 'TB. Miệng';
			$cotDiemHeaders['midTerm2']   = 'Điểm 1 Tiết';
			$cotDiemHeaders['finalTerm2'] = 'Thi HK2';
			$cotDiemHeaders['tbGLTerm2']  = 'TB. Giáo Lý HK2';
			$cotDiemHeaders['tbGLTerm1']  = 'TB. Giáo Lý HK1';
			$cotDiemHeaders['tbGLYear']   = 'TB. Giáo Lý Năm';
			
			$cotDiemHeaders['tbTerm2']           = 'TB. HK2';
			$cotDiemHeaders['tbTerm1']           = 'TB. HK1';
			$cotDiemHeaders['tbYear']            = 'TB. NĂM';
			$cotDiemHeaders['sundayTicketTerm2'] = 'Phiếu CN';
			$cotDiemHeaders['sundayTickets']     = 'Phiếu Cả năm';
			$cotDiemHeaders['category']          = 'Xếp-loại';
			$cotDiemHeaders['awarded']           = 'Khen-thưởng';
			
			$cotDiemAttrs['cc1']        = 'cc1';
			$cotDiemAttrs['cc2']        = 'cc2';
			$cotDiemAttrs['cc3']        = 'cc3';
			$cotDiemAttrs['cc4']        = 'cc4';
			$cotDiemAttrs['cc5']        = 'cc5';
			$cotDiemAttrs['tbCCTerm2']  = 'tbCCTerm2';
			$cotDiemAttrs['quizTerm2']  = 'quizTerm2';
			$cotDiemAttrs['midTerm2']   = 'midTerm2';
			$cotDiemAttrs['finalTerm2'] = 'finalTerm2';
			$cotDiemAttrs['tbGLTerm2']  = 'tbGLTerm2';
			$cotDiemAttrs['tbGLTerm1']  = 'tbGLTerm1';
			$cotDiemAttrs['tbGLYear']   = 'tbGLYear';
			
			$cotDiemAttrs['tbTerm2']           = 'tbTerm2';
			$cotDiemAttrs['tbTerm1']           = 'tbTerm1';
			$cotDiemAttrs['tbYear']            = 'tbYear';
			$cotDiemAttrs['sundayTicketTerm2'] = 'sundayTicketTerm2';
			$cotDiemAttrs['sundayTickets']     = 'sundayTickets';
			$cotDiemAttrs['category']          = 'category';
			$cotDiemAttrs['awarded']           = 'awarded';
			
			$cotDiemLabels['cc1']        = 'điểm Chuyên-cần tháng 1';
			$cotDiemLabels['cc2']        = 'điểm Chuyên-cần tháng 2';
			$cotDiemLabels['cc3']        = 'điểm Chuyên-cần tháng 3';
			$cotDiemLabels['cc4']        = 'điểm Chuyên-cần tháng 4';
			$cotDiemLabels['cc5']        = 'điểm Chuyên-cần tháng 5';
			$cotDiemLabels['tbCCTerm2']  = 'điểm Trung-bình Chuyên-cần';
			$cotDiemLabels['quizTerm2']  = 'điểm Trung-bình Miệng';
			$cotDiemLabels['midTerm2']   = 'điểm 1 Tiết/Giữa-kỳ';
			$cotDiemLabels['finalTerm2'] = 'điểm Thi Cuối-kỳ';
			$cotDiemLabels['tbGLTerm2']  = 'TB. Giáo Lý HK2';
			$cotDiemLabels['tbGLTerm1']  = 'TB. GL HK1';
			$cotDiemLabels['tbGLYear']   = 'TB. GL Cả Năm';
			
			$cotDiemLabels['tbTerm2'] = 'điểm Trung-bình Học-kỳ 2';
			$cotDiemLabels['tbTerm1'] = 'TB. HK1';
			
			$cotDiemLabels['tbYear']            = 'TB. NĂM';
			$cotDiemLabels['sundayTicketTerm2'] = 'phiếu lễ Chúa-nhật';
			$cotDiemLabels['sundayTickets']     = 'phiếu lễ CN Cả Năm';
			$cotDiemLabels['category']          = 'Xếp-loại';
			$cotDiemLabels['awarded']           = 'Khen-thưởng';
			
			$cotDiemCellFormats ['cc1']        = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc2']        = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc3']        = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc4']        = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['cc5']        = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbCCTerm2']  = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['quizTerm2']  = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['midTerm2']   = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.0'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['finalTerm2'] = "renderer: 'darkenEmptyRenderer',type:'numeric', format: '0,0.00'" . ($readOnly ? ', readOnly: true' : '');
			$cotDiemCellFormats ['tbGLTerm2']  = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['tbGLTerm1']  = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats ['tbGLYear']   = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			
			$cotDiemCellFormats ['tbTerm2'] = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			
			$cotDiemCellFormats ['tbTerm1'] = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			$cotDiemCellFormats['tbYear']   = "renderer: 'darkenEmptyRenderer',type:'numeric',readOnly:true, format: '0,0.00'";
			
			$cotDiemCellFormats ['sundayTicketTerm2'] = "renderer: 'darkenEmptyRenderer',type:'numeric'";
			$cotDiemCellFormats ['sundayTickets']     = "renderer: 'darkenEmptyRenderer',type:'numeric', readOnly:true";
			
			$cotDiemCellFormats ['category'] = "type:'text', readOnly:true, renderer: 'xepLoaiRenderer'";
			$cotDiemCellFormats ['awarded']  = "type:'checkbox', readOnly:true";
		}
		
		$cacCotDiemBiLoaiBo = $chiDoan->getCotDiemBiLoaiBo();
		if(is_array($cacCotDiemBiLoaiBo)) {
			foreach($cacCotDiemBiLoaiBo as $cotDiemBiLoaiBo) {
				unset($cotDiemHeaders[ $cotDiemBiLoaiBo ], $cotDiemAttrs[ $cotDiemBiLoaiBo ], $cotDiemCellFormats[ $cotDiemBiLoaiBo ], $cotDiemLabels[ $cotDiemBiLoaiBo ]);
			}
		}
		
		return [ 'readOnly' => $readOnly, 'hocKy' => $hocKy ];
	}
	
	public function ghiDiem(Request $request, &$cotDiemHeaders, &$cotDiemAttrs, &$cotDiemLabels, &$cotDiemCellFormats, $params) {
		$readOnly = $params['readOnly'];
		$hocKy    = $params['hocKy'];
		
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
			
			/** @var BangDiem $bangDiem */
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
				$tbCC     = $bangDiem->getTbCCTerm1();
				$tbGL     = $bangDiem->getTbGLTerm1();
				$tbGLYear = '';
				
				$tbTerm        = $bangDiem->getTbTerm1();
				$tbYear        = 0;
				$sundayTickets = $bangDiem->getSundayTickets();
				$category      = '';
				$retention     = '';
				$awarded       =  false;
			} elseif($hocKy === 2) {
				$tbCC     = $bangDiem->getTbCCTerm2();
				$tbGL     = $bangDiem->getTbGLTerm2();
				$tbGLYear = $bangDiem->getTbGLYear();
				
				$tbTerm        = $bangDiem->getTbTerm2();
				$tbYear        = $bangDiem->getTbYear();
				$sundayTickets = $bangDiem->getSundayTickets();
				$category      = $bangDiem->getCategory();
				$retention     = $bangDiem->isGradeRetention();
				$awarded       = $bangDiem->isAwarded()?:false;
			}
			
			$manager->persist($bangDiem);
//				$manager->persist($phanBo);
//				$manager->persist($chiDoan);
			$manager->flush();

//
			return new JsonResponse([
				'tbCC'          => $tbCC,
				'tbGL'          => $tbGL,
				'tbGLYear'      => $tbGLYear,
				'tbTerm'        => $tbTerm,
				'tbYear'        => $tbYear,
				'sundayTickets' => $sundayTickets,
				'category'      => $category,
				'awarded'       => $awarded
			], 200);
		} else {
			return new JsonResponse([ 404, 'Không thể tìm thấy Thiếu-nhi này' ], 404);
		}
	}
}