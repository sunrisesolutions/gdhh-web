<?php

namespace App\Entity\HoSo;

use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__chi_doan")
 */
class ChiDoan {
	
	/**
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
	
	public function getSoThieuNhiNgheo() {
		if($this->soThieuNhiNgheo === null) {
			$this->soThieuNhiNgheo = 0;
			/** @var PhanBo $phanBo */
			foreach($this->phanBoHangNam as $phanBo) {
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
			foreach($this->phanBoHangNam as $phanBo) {
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
			foreach($this->phanBoHangNam as $phanBo) {
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
			foreach($this->phanBoHangNam as $phanBo) {
				if($phanBo->getThanhVien()->isEnabled()) {
					$counter ++;
				}
			}
			$this->soThieuNhi = $counter;
		}
		
		return $this->soThieuNhi;
	}
	
	function __construct() {
		$this->phanBoHangNam    = new ArrayCollection();
		$this->cacDoiNhomGiaoLy = new ArrayCollection();
	}
	
	public function generateId() {
		$this->id = $this->number . '-' . $this->namHoc->getId();
	}
	
	private $phanBoHangNamSorted = false;
	
	/** @var ArrayCollection */
	private $phanBoThieuNhi;
	
	public function getPhanBoThieuNhi($enabled = null) {
		$pb = $this->getPhanBoHangNam();
		if( ! empty($this->phanBoThieuNhi)) {
			return $this->phanBoThieuNhi;
		}

		$this->phanBoThieuNhi = new ArrayCollection();
		/** @var PhanBo $phanBo */
		foreach($pb as $phanBo) {
			if($phanBo->getThanhVien()->isThieuNhi()) {
				if($enabled === null || $enabled && $phanBo->getThanhVien()->isEnabled() || ! $enabled && ! $phanBo->getThanhVien()->isEnabled()) {
					$phanBo->createBangDiem();
					$this->phanBoThieuNhi->add($phanBo);
				}
			}
		}
		
		return $this->phanBoThieuNhi;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public
	function getPhanBoHangNam() {
//		if( ! $this->phanBoHangNamSorted) {
//			$array       = $this->phanBoHangNam->toArray();
//			$phanBoArray = [];
//			$sortedArray = [];
//			$returnArray = [];
//			/** @var PhanBo $phanBo */
//			foreach($array as $phanBo) {
//				$firstName                       = $phanBo->getThanhVien()->getFirstname();
//				$sortedArray[ $phanBo->getId() ] = $firstName;
//				$phanBoArray[ $phanBo->getId() ] = $phanBo;
//			}
//			$this->phanBoHangNamSorted = true;
//			$collator                  = new \Collator('vi_VN');
////			natcasesort()
//			$collator->asort($sortedArray);
//			foreach($sortedArray as $id => $name) {
//				$returnArray[] = $phanBoArray[ $id ];
//			}
//			$this->phanBoHangNam = new ArrayCollection(($returnArray));
//		}
		
		return $this->phanBoHangNam;
	}
	
	public function chiaTruongPhuTrachVaoCacDoi($doi1, $doi2, $doi3, $doi4, $doi5, $doi6, $doi7, $doi8, PhanBo $phanBo) {
		if($phanBo->getChiDoan()->getId() !== $this->id) {
			return false;
		}
		$phanBo->clearCacTruongPhuTrachDoi();
		$dngl1 = $this->getDoiNhomGiaoLy($doi1);
		$dngl2 = $this->getDoiNhomGiaoLy($doi2);
		$dngl3 = $this->getDoiNhomGiaoLy($doi3);
		$dngl4 = $this->getDoiNhomGiaoLy($doi4);
		$dngl5 = $this->getDoiNhomGiaoLy($doi5);
		$dngl6 = $this->getDoiNhomGiaoLy($doi6);
		$dngl7 = $this->getDoiNhomGiaoLy($doi7);
		$dngl8 = $this->getDoiNhomGiaoLy($doi8);
		
		$truongPT1 = $this->chiaTruongPhuTrach($dngl1, $phanBo);
		$truongPT2 = $this->chiaTruongPhuTrach($dngl2, $phanBo);
		$truongPT3 = $this->chiaTruongPhuTrach($dngl3, $phanBo);
		$truongPT4 = $this->chiaTruongPhuTrach($dngl4, $phanBo);
		$truongPT5 = $this->chiaTruongPhuTrach($dngl5, $phanBo);
		$truongPT6 = $this->chiaTruongPhuTrach($dngl6, $phanBo);
		$truongPT7 = $this->chiaTruongPhuTrach($dngl7, $phanBo);
		$truongPT8 = $this->chiaTruongPhuTrach($dngl8, $phanBo);
		
		return ! empty($truongPT1) || ! empty($truongPT2) || ! empty($truongPT3) || ! empty($truongPT4) || ! empty($truongPT5) || ! empty($truongPT6) || ! empty($truongPT7) || ! empty($truongPT8);
	}
	
	public function hoanTatBangDiemHK1($forced = false) {
		/** @var PhanBo $phanBo */
		foreach($this->phanBoHangNam as $phanBo) {
			if($phanBo->isHuynhTruong() && $phanBo->getThanhVien()->isEnabled()) {
				$cacTruong = $phanBo->getCacTruongPhuTrachDoi();
				/** @var TruongPhuTrachDoi $truongPT */
				foreach($cacTruong as $truongPT) {
					if($forced) {
						$truongPT->getDoiNhomGiaoLy()->setHoanTatBangDiemHK1(true);
					} elseif( ! $truongPT->getDoiNhomGiaoLy()->isHoanTatBangDiemHK1()) {
						return false;
					}
				}
			}
		}
		$this->hoanTatBangDiemHK1 = true;
		
		return true;
	}
	
	public function hoanTatBangDiemHK2($forced = false) {
		/** @var PhanBo $phanBo */
		foreach($this->phanBoHangNam as $phanBo) {
			if($phanBo->isHuynhTruong() && $phanBo->getThanhVien()->isEnabled()) {
				$cacTruong = $phanBo->getCacTruongPhuTrachDoi();
				/** @var TruongPhuTrachDoi $truongPT */
				foreach($cacTruong as $truongPT) {
					if($forced) {
						$truongPT->getDoiNhomGiaoLy()->setHoanTatBangDiemHK2(true);
					} elseif( ! $truongPT->getDoiNhomGiaoLy()->isHoanTatBangDiemHK2()) {
						return false;
					}
				}
			}
		}
		$this->hoanTatBangDiemHK2 = true;
		
		return true;
	}
	
	public function getHocKyHienTai() {
		$hocKy = 1;
		if($this->duocDuyetBangDiemHK1) {
			$hocKy = 2;
		}
		
		return $hocKy;
	}
	
	/**
	 * @param DoiNhomGiaoLy $dngl
	 * @param PhanBo        $phanBo
	 *
	 * @return TruongPhuTrachDoi|bool
	 */
	public function chiaTruongPhuTrach(DoiNhomGiaoLy $dngl = null, PhanBo $phanBo) {
		if( ! empty($dngl)) {
			$truongPhuTrach = new TruongPhuTrachDoi();
			$truongPhuTrach->setPhanBo($phanBo);
			$truongPhuTrach->setDoiNhomGiaoLy($dngl);
			$truongPhuTrach->generateId();
			
			$dngl->getCacTruongPhuTrachDoi()->add($truongPhuTrach);
			$phanBo->getCacTruongPhuTrachDoi()->add($truongPhuTrach);
			
			return $truongPhuTrach;
		}
		
		return false;
	}
	
	/**
	 * @param integer $doi
	 *
	 * @return DoiNhomGiaoLy
	 */
	public function getDoiNhomGiaoLy($doi, $autoCreate = true) {
		if(empty($doi)) {
			return null;
		}
		/** @var DoiNhomGiaoLy $dngl */
		foreach($this->cacDoiNhomGiaoLy as $dngl) {
			if($dngl->getNumber() === $doi) {
				return $dngl;
			}
		}
		if(empty($autoCreate)) {
			return null;
		}
		$dngl = new DoiNhomGiaoLy();
		$dngl->setNumber($doi);
		$dngl->setChiDoan($this);
		$this->cacDoiNhomGiaoLy->add($dngl);
		$dngl->generateId();
		
		return $dngl;
	}
	
	/**
	 * @return mixed
	 */
	public
	function getId() {
		return $this->id;
	}
	
	/** @var array
	 * @ORM\Column(type="json")
	 */
	protected
		$cotDiemBiLoaiBo = array();
	
	/**
	 * @var NamHoc
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\NamHoc",inversedBy="chiDoan")
	 * @ORM\JoinColumn(name="nam_hoc", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected
		$namHoc;
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\DoiNhomGiaoLy", mappedBy="chiDoan", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected
		$cacDoiNhomGiaoLy;
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\PhanBo", mappedBy="chiDoan", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected
		$phanBoHangNam;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $hoanTatBangDiemHK1 = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duocDuyetBangDiemHK1 = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $hoanTatBangDiemHK2 = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duocDuyetBangDiemHK2 = false;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected
		$name;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected
		$number;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected
		$phanDoan;
	
	/**
	 * @return string
	 */
	public
	function getPhanDoan() {
		return $this->phanDoan;
	}
	
	/**
	 * @param string $phanDoan
	 */
	public
	function setPhanDoan(
		$phanDoan
	) {
		$this->phanDoan = $phanDoan;
	}
	
	/**
	 * @param ArrayCollection $phanBoHangNam
	 */
	public
	function setPhanBoHangNam(
		$phanBoHangNam
	) {
		$this->phanBoHangNam = $phanBoHangNam;
	}
	
	/**
	 * @return NamHoc
	 */
	public
	function getNamHoc() {
		return $this->namHoc;
	}
	
	/**
	 * @param NamHoc $namHoc
	 */
	public
	function setNamHoc(
		$namHoc
	) {
		$this->namHoc = $namHoc;
	}
	
	/**
	 * @return string
	 */
	public
	function getName() {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 */
	public
	function setName(
		$name
	) {
		$this->name = $name;
	}
	
	/**
	 * @return array
	 */
	public
	function getCotDiemBiLoaiBo() {
		return $this->cotDiemBiLoaiBo;
	}
	
	/**
	 * @param array $cotDiemBiLoaiBo
	 */
	public
	function setCotDiemBiLoaiBo(
		$cotDiemBiLoaiBo
	) {
		$this->cotDiemBiLoaiBo = $cotDiemBiLoaiBo;
	}
	
	/**
	 * @return int
	 */
	public
	function getNumber() {
		return $this->number;
	}
	
	/**
	 * @param int $number
	 */
	public
	function setNumber(
		$number
	) {
		$this->number = $number;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getCacDoiNhomGiaoLy() {
		return $this->cacDoiNhomGiaoLy;
	}
	
	/**
	 * @param ArrayCollection $cacDoiNhomGiaoLy
	 */
	public function setCacDoiNhomGiaoLy($cacDoiNhomGiaoLy) {
		$this->cacDoiNhomGiaoLy = $cacDoiNhomGiaoLy;
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
	public function isDuocDuyetBangDiemHK1() {
		return $this->duocDuyetBangDiemHK1;
	}
	
	/**
	 * @param bool $duocDuyetBangDiemHK1
	 */
	public function setDuocDuyetBangDiemHK1($duocDuyetBangDiemHK1) {
		$this->duocDuyetBangDiemHK1 = $duocDuyetBangDiemHK1;
	}
	
	/**
	 * @return bool
	 */
	public function isDuocDuyetBangDiemHK2() {
		return $this->duocDuyetBangDiemHK2;
	}
	
	/**
	 * @param bool $duocDuyetBangDiemHK2
	 */
	public function setDuocDuyetBangDiemHK2($duocDuyetBangDiemHK2) {
		$this->duocDuyetBangDiemHK2 = $duocDuyetBangDiemHK2;
	}
	
}