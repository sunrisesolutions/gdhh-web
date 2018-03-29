<?php

namespace App\Helper;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\PhanBo;
use App\Service\BaseService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BangDiemHandsonTableService extends BaseService {
	
	/**
	 * @deprecated
	 *
	 * @param PhanBo $phanBo
	 * @param        $cotDiemHeaders
	 * @param        $cotDiemAttrs
	 * @param        $cotDiemLabels
	 * @param        $cotDiemCellFormats
	 *
	 * @return array
	 */
	public function prepareTable(PhanBo $phanBo, &$cotDiemHeaders, &$cotDiemAttrs, &$cotDiemLabels, &$cotDiemCellFormats) {
		$chiDoan  = $phanBo->getChiDoan();
		$hocKy    = $chiDoan->getHocKyHienTai();
		$readOnly = $phanBo->getThanhVien()->getHuynhTruongObj()->isBangDiemReadOnly();
		if($hocKy === 1) {
		} elseif($hocKy === 2) {

		}
		
		$cacCotDiem = $phanBo->getThanhVien()->getHuynhTruongObj()->getBangDiemSpreadsheet()->getCacCotDiem($hocKy, $readOnly);
		
		foreach($cacCotDiem as $key => $prop) {
			$cotDiemHeaders[ $key ] = $prop['header'];
			$cotDiemLabels[ $key ]  = $prop['label'];
			$cotDiemAttrs[ $key ]   = $prop['attr'];
			$cotDiemCellFormats[ $key ]   = $prop['format'];
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
				$retention     = null;
				$awarded       = false;
			} elseif($hocKy === 2) {
				$tbCC     = $bangDiem->getTbCCTerm2();
				$tbGL     = $bangDiem->getTbGLTerm2();
				$tbGLYear = $bangDiem->getTbGLYear();
				
				$tbTerm        = $bangDiem->getTbTerm2();
				$tbYear        = $bangDiem->getTbYear();
				$sundayTickets = $bangDiem->getSundayTickets();
				$category      = $bangDiem->getCategory();
				$retention     = $bangDiem->isGradeRetention();
				$awarded       = $bangDiem->isAwarded() ?: false;
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
				'categoryTrans'      => $this->get('translator')->trans($category,[],'BinhLeAdmin'),
				'awarded'       => $awarded,
				'retention'     => $retention
			], 200);
		} else {
			return new JsonResponse([ 404, 'Không thể tìm thấy Thiếu-nhi này' ], 404);
		}
	}
}