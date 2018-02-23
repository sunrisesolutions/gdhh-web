<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HuynhTruongAdminController extends BaseCRUDAdminController {
	public function truongChiDoanAction(ChiDoan $chiDoan, Request $request) {
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		$admin->setAction('truong-chi-doan');
		$admin->setActionParams([ 'chiDoan' => $chiDoan ]);
		if( ! empty($namHoc = $this->get(NamHocService::class)->getNamHocHienTai())) {
			$admin->setNamHoc($namHoc->getId());
		}
		
		return parent::listAction();
	}
	
	public function truongPhanDoanAction($phanDoan, Request $request) {
		$phanDoan = strtoupper($phanDoan);
		$cd = ThanhVien::getDanhSachChiDoanTheoPhanDoan($phanDoan);
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		$admin->setAction('truong-phan-doan');
		$admin->setActionParams([ 'danhSachChiDoan' => $cd ]);
		if( ! empty($namHoc = $this->get(NamHocService::class)->getNamHocHienTai())) {
			$admin->setNamHoc($namHoc->getId());
		}
		
		return parent::listAction();
		
		
	}
	
	
}