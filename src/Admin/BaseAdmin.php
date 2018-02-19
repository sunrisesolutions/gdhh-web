<?php

namespace App\Admin;

use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Service\User\UserService;
use Sonata\AdminBundle\Admin\AbstractAdmin;

class BaseAdmin extends AbstractAdmin {
	const ENTITY = null;
	const CONTROLLER = null;
	/** @var ThanhVien $thanhVien */
	protected $thanhVien = null;
	
	public function toString($object) {
		if($object instanceof ThanhVien) {
			return $object->getName();
		}
		if($object instanceof PhanBo) {
			return $object->getThanhVien()->getName();
		}
		if($object instanceof DoiNhomGiaoLy) {
			$str = 'Đội của ';
			$str .= $object->getTenCacTruongPhuTrach();
			
			return $str;
		}
		
		return parent::toString($object);
	}
	
	protected function getUserThanhVien() {
		if(empty($this->thanhVien)) {
			$container       = $this->getConfigurationPool()->getContainer();
			$user            = $container->get(UserService::class)->getUser();
			$this->thanhVien = $user->getThanhVien();
		}
		
		return $this->thanhVien;
	}
	
	protected function getUserChiDoan() {
		
		return $this->getUserThanhVien()->getChiDoan();
		
	}
	
}