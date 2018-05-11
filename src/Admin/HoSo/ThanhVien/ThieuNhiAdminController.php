<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ThieuNhiAdminController extends BaseCRUDAdminController {
	public function thieuNhiNhomAction(PhanBo $phanBo, Request $request) {
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		
		$cacDoiNhomGiaoLy = $phanBo->getCacDoiNhomGiaoLyPhuTrach();
		
		$admin->setAction('list-thieu-nhi-nhom');
		$admin->setActionParams([
			'phanBo'           => $phanBo,
			'cacDoiNhomGiaoLy' => $cacDoiNhomGiaoLy,
			'chiDoan'          => $phanBo->getChiDoan()
		]);
		
		return parent::listAction();
	}
	
	public function thieuNhiChiDoanAction(PhanBo $phanBo, Request $request) {
		
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		
		$admin->setAction('list-thieu-nhi-chi-doan');
		$admin->setActionParams([
			'phanBo'  => $phanBo,
			'chiDoan' => $phanBo->getChiDoan()
		]);
		
		return parent::listAction();
	}
	
	public function thieuNhiPhanDoanAction($phanDoan, Request $request) {
		$actionParams                    = [ 'phanDoan' => $phanDoan ];
		$phanDoan                        = strtoupper($phanDoan);
		$cd                              = ThanhVien::getDanhSachChiDoanTheoPhanDoan($phanDoan);
		$actionParams['danhSachChiDoan'] = $cd;
		
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		$admin->setAction('list-thieu-nhi-phan-doan');
		$admin->setActionParams($actionParams);
		
		if( ! empty($namHoc = $this->get(NamHocService::class)->getNamHocHienTai())) {
			$admin->setNamHoc($namHoc->getId());
		}
		
		return parent::listAction();
		
		
	}
}