<?php

namespace App\Service\HoSo\ThanhVien;

use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\ThanhVien\HuynhTruong;
use App\Service\BaseService;

class HuynhTruongService extends BaseService {
	public function addThieuNhi(ThanhVien $object, ThanhVien $thieuNhi) {
		if(empty($object)) {
			$object->setThieuNhi(true);
			$object->setHuynhTruong(false);
		} else {
			$object->getHuynhTruongObj()->addThieuNhi($thieuNhi);
		}
	}
}