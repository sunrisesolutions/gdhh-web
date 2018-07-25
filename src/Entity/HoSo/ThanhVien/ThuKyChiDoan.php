<?php

namespace App\Entity\HoSo\ThanhVien;

use App\Entity\HocBa\BangDiemSpreadsheet\BangDiemChiDoanWriter;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Doctrine\Common\Collections\ArrayCollection;

class ThuKyChiDoan extends HuynhTruong {
	
	protected function createBangDiemWriter() {
		return new BangDiemChiDoanWriter($this);
	}
	
	public function isThieuNhiCungChiDoan(PhanBo $phanBo) {
		if(empty($this->phanBo)) {
			return false;
		}
		
		return $this->phanBo->getChiDoan()->getId() === $phanBo->getChiDoan()->getId();
	}
	
	/**
	 * @param NamHoc|null $namHocObj
	 *
	 * @return ArrayCollection|null
	 */
	public function getCacPhanBoThieuNhiPhuTrach(NamHoc $namHocObj = null, $phaiCoDoi = false) {
		if(empty($namHocObj)) {
			$namHoc = 0;
		}else{
			$namHoc = $namHocObj->getId();
		}
		
		if(array_key_exists($namHoc, $this->cacPhanBoThieuNhiPhuTrachTheoNamHoc)) {
			return $this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[0];
		}
		
		if(empty($this->phanBo)) {
			return null;
		}
		
		if(empty($phaiCoDoi)) {
			$this->cacPhanBoThieuNhiPhuTrachTheoNamHoc[ $namHoc ] = $this->phanBo->sortCacPhanBo($this->phanBo->getChiDoan()->getPhanBoThieuNhi(true));
			
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
	
	
	/**
	 * @param $hocKy
	 *
	 * @return bool
	 */
	public function coTheNopBangDiem($hocKy) {
		$hocKy   = intval($hocKy);
		$chiDoan = $this->phanBo->getChiDoan();
		
		if($hocKy === 1 && $chiDoan->isHoanTatBangDiemHK1()) {
			return false;
		}
		
		if($hocKy === 2 && $chiDoan->isHoanTatBangDiemHK2()) {
			return false;
		}
		
		return true;
	}
}