<?php

namespace App\Entity\HocBa\BangDiemSpreadsheet;

use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien\HuynhTruong;
use App\Entity\HoSo\TruongPhuTrachDoi;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class BangDiemXuDoanWriter extends BangDiemPhanDoanWriter {
	
	public function writeHeading($hocKy, $namHocId, ChiDoan $chiDoan = null, HuynhTruong $truong = null) {
		if(empty($truong)) {
			$truong = $this->huynhTruong;
		}
		$chiDoan = $truong->getPhanBo()->getChiDoan();
		$this->writeBaseHeading($hocKy, $namHocId, $chiDoan, $truong);
		$sWriter = $this->sWriter;
		$sWriter->goDown();
		$sWriter->goDown();
	}
	
}