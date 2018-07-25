<?php

namespace App\Entity\HoSo;

use App\Entity\Content\Base\AppContentEntity;
use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__nam_hoc")
 */
class NamHoc {
	
	/** @var NamHoc */
	private static $namHienTai;
	
	public function getNamHocHienTai() {
		if(empty(self::$namHienTai)) {
			if($this->started && $this->enabled) {
				self::$namHienTai = $this;
			}
			
			if(empty($this->namSau)) {
				return null;
			} else {
				if($this->namSau->started && $this->namSau->enabled) {
					self::$namHienTai = $this->namSau;
				} else {
					$this->namSau->getNamHocHienTai();
				}
			}
		}
		
		return self::$namHienTai;
	}
	
	/**
	 * ID_REF
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 */
	protected $id;
	
	/**
	 * @param mixed $id
	 */
	public function setId($id) {
		$this->id = $id;
	}
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	function __construct() {
		$this->chiDoan = new ArrayCollection();
	}
	
	public function getChiDoanWithNumber($number) {
		if(is_int($number)) {
			/** @var ChiDoan $cd */
			foreach($this->chiDoan as $cd) {
				if($cd->getNumber() === $number) {
					return $cd;
				}
			}
			
			$cd = new ChiDoan();
			$cd->setNamHoc($this);
			$cd->setName('' . $number);
			$cd->setNumber($number);
			$cd->setPhanDoan(ThanhVien::$danhSachChiDoan[ $number ]);
			$cd->generateId();
			$this->chiDoan->add($cd);
			
			return $cd;
		}
		
		return null;
	}
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\ChiDoan", mappedBy="namHoc", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected $chiDoan;
	
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\PhanBo", mappedBy="namHoc", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected $phanBoHangNam;
	
	
	/**
	 * @var NamHoc
	 * @ORM\OneToOne(targetEntity="App\Entity\HoSo\NamHoc", mappedBy="namTruoc", cascade={"persist","merge"})
	 */
	protected $namSau;
	
	/**
	 * @var NamHoc
	 * @ORM\OneToOne(targetEntity="App\Entity\HoSo\NamHoc", inversedBy="namSau", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_nam_truoc", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $namTruoc;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false} )
	 */
	protected $started = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false} )
	 */
	protected $enabled = false;
	
	/** @var integer
	 * @ORM\Column(type="integer", options={"default":120000} )
	 */
	protected $tienQuy = 120000;
	
	/** @var float
	 * @ORM\Column(type="float", options={"default":5} )
	 */
	protected $diemTB = 5;
	
	/** @var float
	 * @ORM\Column(type="float", options={"default":7.5} )
	 */
	protected $diemKha = 7.5;
	
	/** @var float
	 * @ORM\Column(type="float", options={"default":8.75} )
	 */
	protected $diemGioi = 8.75;
	
	/** @var integer
	 * @ORM\Column(type="integer", options={"default":15} )
	 */
	protected $phieuLenLop = 15;
	
	/** @var integer
	 * @ORM\Column(type="integer", options={"default":25} )
	 */
	protected $phieuKhenThuong = 25;
	
	/**
	 * @return bool
	 */
	public function isEnabled() {
		return $this->enabled;
	}
	
	/**
	 * @param bool $enabled
	 */
	public function setEnabled($enabled) {
		$this->enabled = $enabled;
	}
	
	
	/**
	 * @return ArrayCollection
	 */
	public function getChiDoan() {
		return $this->chiDoan;
	}
	
	/**
	 * @param ArrayCollection $chiDoan
	 */
	public function setChiDoan($chiDoan) {
		$this->chiDoan = $chiDoan;
	}
	
	/**
	 * @return float
	 */
	public function getDiemTB() {
		return $this->diemTB;
	}
	
	/**
	 * @param float $diemTB
	 */
	public function setDiemTB($diemTB) {
		$this->diemTB = $diemTB;
	}
	
	/**
	 * @return float
	 */
	public function getDiemKha() {
		return $this->diemKha;
	}
	
	/**
	 * @param float $diemKha
	 */
	public function setDiemKha($diemKha) {
		$this->diemKha = $diemKha;
	}
	
	/**
	 * @return float
	 */
	public function getDiemGioi() {
		return $this->diemGioi;
	}
	
	/**
	 * @param float $diemGioi
	 */
	public function setDiemGioi($diemGioi) {
		$this->diemGioi = $diemGioi;
	}
	
	/**
	 * @return int
	 */
	public function getPhieuLenLop() {
		return $this->phieuLenLop;
	}
	
	/**
	 * @param int $phieuLenLop
	 */
	public function setPhieuLenLop($phieuLenLop) {
		$this->phieuLenLop = $phieuLenLop;
	}
	
	/**
	 * @return int
	 */
	public function getPhieuKhenThuong() {
		return $this->phieuKhenThuong;
	}
	
	/**
	 * @param int $phieuKhenThuong
	 */
	public function setPhieuKhenThuong($phieuKhenThuong) {
		$this->phieuKhenThuong = $phieuKhenThuong;
	}
	
	/**
	 * @return bool
	 */
	public function isStarted() {
		return $this->started;
	}
	
	/**
	 * @param bool $started
	 */
	public function setStarted($started) {
		$this->started = $started;
	}
	
	/**
	 * @return NamHoc
	 */
	public function getNamSau() {
		return $this->namSau;
	}
	
	/**
	 * @param NamHoc $namSau
	 */
	public function setNamSau($namSau) {
		$this->namSau = $namSau;
	}
	
	/**
	 * @return NamHoc
	 */
	public function getNamTruoc() {
		return $this->namTruoc;
	}
	
	/**
	 * @param NamHoc $namTruoc
	 */
	public function setNamTruoc($namTruoc) {
		$this->namTruoc = $namTruoc;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getPhanBoHangNam() {
		return $this->phanBoHangNam;
	}
	
	/**
	 * @param ArrayCollection $phanBoHangNam
	 */
	public function setPhanBoHangNam($phanBoHangNam) {
		$this->phanBoHangNam = $phanBoHangNam;
	}
	
	/**
	 * @return int
	 */
	public function getTienQuy() {
		return $this->tienQuy;
	}
	
	/**
	 * @param int $tienQuy
	 */
	public function setTienQuy($tienQuy) {
		$this->tienQuy = $tienQuy;
	}
}