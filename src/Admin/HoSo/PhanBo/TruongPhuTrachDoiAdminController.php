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
	
	
}