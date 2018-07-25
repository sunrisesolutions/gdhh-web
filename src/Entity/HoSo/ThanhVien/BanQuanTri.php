<?php

namespace App\Entity\HoSo\ThanhVien;

use App\Entity\HocBa\BangDiemSpreadsheet\BangDiemPhanDoanWriter;
use App\Entity\HocBa\BangDiemSpreadsheet\BangDiemXuDoanWriter;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\ThanhVien;
use Doctrine\Common\Collections\ArrayCollection;

class BanQuanTri extends ChiDoanTruong {
	protected function createBangDiemWriter() {
		return new BangDiemXuDoanWriter($this);
	}
	
	public function getCacPhanBoThieuNhiPhuTrach(NamHoc $namHocObj = null, $phaiCoDoi = false) {
		if(empty($namHocObj)) {
			$namHoc = 0;
		} else {
			$namHoc = $namHocObj->getId();
		}
		
		if(array_key_exists($namHoc, $this->cacPhanBoThieuNhiPhuTrachTheoNamHoc)) {
			return $this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[0];
		}
		
		if(empty($this->phanBo)) {
			return null;
		}
		
		if(empty($phaiCoDoi)) {
			$dsChiDoan           = $this->phanBo->getNamHoc()->getChiDoan();
			$phanBoThieuNhiArray = [];
			/** @var ChiDoan $cd */
			foreach($dsChiDoan as $cd) {
				$phanBoThieuNhiArray = array_merge($phanBoThieuNhiArray, $cd->getPhanBoThieuNhi(true)->toArray());
			}
			
			$this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[ $namHoc ] = $this->phanBo->sortCacPhanBo(new ArrayCollection($phanBoThieuNhiArray));
			
			return $this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[ $namHoc ];
		}
		
		$phanBoThieuNhi = new ArrayCollection();
		$cacDngl        = $this->phanBo->getChiDoan()->getCacDoiNhomGiaoLy();
		/** @var DoiNhomGiaoLy $dngl */
		foreach($cacDngl as $dngl) {
			$phanBoThieuNhi = new ArrayCollection(array_merge($phanBoThieuNhi->toArray(), $dngl->getPhanBoThieuNhi(true)->toArray()));
		}
		
		$this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[ $namHoc ] = $phanBoThieuNhi = PhanBo::sortCacPhanBo($phanBoThieuNhi);
		
		return $phanBoThieuNhi;
	}
	
}