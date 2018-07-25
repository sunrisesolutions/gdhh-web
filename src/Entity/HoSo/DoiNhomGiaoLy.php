<?php

namespace App\Entity\HoSo;

use App\Entity\Content\Base\AppContentEntity;
use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__doi_nhom_giao_ly")
 */
class DoiNhomGiaoLy {
	
	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 */
	protected $id;
	
	
	/** @var integer */
	private $soThieuNhi = null;
	/** @var integer */
	private $soThieuNhiDongQuy = null;
	/** @var integer */
	private $soTienQuyDaDong = null;
	/** @var integer */
	private $soThieuNhiNgheo = null;
	
	public function getTenCacTruongPhuTrach() {
		$cacTruong = $this->cacTruongPhuTrachDoi;
		$str       = '';
		/** @var TruongPhuTrachDoi $truong */
		foreach($cacTruong as $truong) {
			$tv  = $truong->getPhanBo()->getThanhVien();
			$str .= $tv->getTitle() . ' ' . $tv->getFirstname() . ' ';
		}
		
		return $str;
	}
	
	public function getSoThieuNhiNgheo() {
		if($this->soThieuNhiNgheo === null) {
			$this->soThieuNhiNgheo = 0;
			/** @var PhanBo $phanBo */
			foreach($this->phanBoThieuNhi as $phanBo) {
				if($phanBo->getThanhVien()->isEnabled()) {
					if($phanBo->isNgheoKho()) {
						$this->soThieuNhiNgheo ++;
					}
				}
			}
		}
		
		return $this->soThieuNhiNgheo;
	}
	
	public function getPhanTramDongQuy() {
		return ($this->getSoThieuNhiDongQuy() / $this->getSoThieuNhi()) * 100;
	}
	
	public function getSoTienQuyDaDong() {
		if($this->soTienQuyDaDong === null) {
			$this->soTienQuyDaDong = 0;
			
			/** @var PhanBo $phanBo */
			foreach($this->phanBoThieuNhi as $phanBo) {
				if($phanBo->getThanhVien()->isEnabled()) {
					if($phanBo->isDaDongQuy()) {
						$this->soTienQuyDaDong += $phanBo->getTienQuyDong();
					}
				}
			}
		}
		
		return $this->soTienQuyDaDong;
	}
	
	public function getSoThieuNhiDongQuy() {
		if($this->soThieuNhiDongQuy === null) {
			$this->soThieuNhi = 0;
			$counter          = 0;
			/** @var PhanBo $phanBo */
			foreach($this->phanBoThieuNhi as $phanBo) {
				if($phanBo->getThanhVien()->isEnabled()) {
					$this->soThieuNhi ++;
					if($phanBo->isDaDongQuy()) {
						$counter ++;
					}
				}
			}
			$this->soThieuNhiDongQuy = $counter;
		}
		
		return $this->soThieuNhiDongQuy;
	}
	
	public function getSoThieuNhi() {
		if($this->soThieuNhi === null) {
			$counter = 0;
			/** @var PhanBo $phanBo */
			foreach($this->phanBoThieuNhi as $phanBo) {
				if($phanBo->getThanhVien()->isEnabled()) {
					$counter ++;
				}
			}
			$this->soThieuNhi = $counter;
		}
		
		return $this->soThieuNhi;
	}
	
	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	
	public function getNamHoc() {
		return $this->chiDoan->getNamHoc();
	}
	
	function __construct() {
		$this->phanBoThieuNhi       = new ArrayCollection();
		$this->cacTruongPhuTrachDoi = new ArrayCollection();
	}
	
	public function generateId() {
		$this->id = $this->number . '-' . $this->chiDoan->getId();
	}
	
	/**
	 * @var ChiDoan
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\ChiDoan", inversedBy="cacDoiNhomGiaoLy", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_chi_doan", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $chiDoan;
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\PhanBo", mappedBy="doiNhomGiaoLy", cascade={"persist","merge"})
	 */
	protected $phanBoThieuNhi;
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\TruongPhuTrachDoi", mappedBy="doiNhomGiaoLy", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected $cacTruongPhuTrachDoi;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duyetBangDiemHK1CDT = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $hoanTatBangDiemHK1 = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duyetBangDiemHK2CDT = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $hoanTatBangDiemHK2 = false;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer")
	 */
	protected $number;
	
	/**
	 * @return ChiDoan
	 */
	public function getChiDoan() {
		return $this->chiDoan;
	}
	
	/**
	 * @param ChiDoan $chiDoan
	 */
	public function setChiDoan($chiDoan) {
		$this->chiDoan = $chiDoan;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getPhanBoThieuNhi() {
		return $this->phanBoThieuNhi;
	}
	
	/**
	 * @param ArrayCollection $phanBoThieuNhi
	 */
	public function setphanBoThieuNhi($phanBoThieuNhi) {
		$this->phanBoThieuNhi = $phanBoThieuNhi;
	}
	
	/**
	 * @return int
	 */
	public function getNumber() {
		return $this->number;
	}
	
	/**
	 * @param int $number
	 */
	public function setNumber($number) {
		$this->number = $number;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getCacTruongPhuTrachDoi() {
		return $this->cacTruongPhuTrachDoi;
	}
	
	/**
	 * @param ArrayCollection $cacTruongPhuTrachDoi
	 */
	public function setCacTruongPhuTrachDoi($cacTruongPhuTrachDoi) {
		$this->cacTruongPhuTrachDoi = $cacTruongPhuTrachDoi;
	}
	
	/**
	 * @return bool
	 */
	public function isHoanTatBangDiemHK1() {
		return $this->hoanTatBangDiemHK1;
	}
	
	/**
	 * @param bool $hoanTatBangDiemHK1
	 */
	public function setHoanTatBangDiemHK1($hoanTatBangDiemHK1) {
		$this->hoanTatBangDiemHK1 = $hoanTatBangDiemHK1;
	}
	
	/**
	 * @return bool
	 */
	public function isHoanTatBangDiemHK2() {
		return $this->hoanTatBangDiemHK2;
	}
	
	/**
	 * @param bool $hoanTatBangDiemHK2
	 */
	public function setHoanTatBangDiemHK2($hoanTatBangDiemHK2) {
		$this->hoanTatBangDiemHK2 = $hoanTatBangDiemHK2;
	}
	
	/**
	 * @return bool
	 */
	public function isDuyetBangDiemHK1CDT() {
		return $this->duyetBangDiemHK1CDT;
	}
	
	/**
	 * @param bool $duyetBangDiemHK1CDT
	 */
	public function setDuyetBangDiemHK1CDT($duyetBangDiemHK1CDT) {
		$this->duyetBangDiemHK1CDT = $duyetBangDiemHK1CDT;
	}
	
	/**
	 * @return bool
	 */
	public function isDuyetBangDiemHK2CDT() {
		return $this->duyetBangDiemHK2CDT;
	}
	
	/**
	 * @param bool $duyetBangDiemHK2CDT
	 */
	public function setDuyetBangDiemHK2CDT($duyetBangDiemHK2CDT) {
		$this->duyetBangDiemHK2CDT = $duyetBangDiemHK2CDT;
	}
	
}