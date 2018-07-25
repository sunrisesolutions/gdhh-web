<?php

namespace App\Entity\HoSo;

use App\Entity\Content\Base\AppContentEntity;
use App\Entity\HoSo\ThanhVien\BanQuanTri;
use App\Entity\HoSo\ThanhVien\ChiDoanTruong;
use App\Entity\HoSo\ThanhVien\HuynhTruong;
use App\Entity\HoSo\ThanhVien\PhanDoanTruong;
use App\Entity\HoSo\ThanhVien\ThuKyChiDoan;
use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__thanh_vien")
 */
class ThanhVien {
	
	public static $christianNameSex = [
		'PHERO'                  => 'NAM',
		'PHAOLO'                 => 'NAM',
		'GIUSE'                  => 'NAM',
		'LUCA'                   => 'NAM',
		'ANRE'                   => 'NAM',
		'GIEROMINO'              => 'NAM',
		'NI-CÔ-LA (SANTA CLAUS)' => 'NAM',
		
		'TERESA'            => 'NỮ',
		'MARIA'             => 'NỮ',
		'ANNA'              => 'NỮ',
		'MARIA MADALENA'    => 'NỮ',
		'MARTINO DE PORRES' => 'NAM',
		'ROSA LIMA'         => 'NỮ',
		
		'DM VINCENTE'      => 'NAM',
		'MARTINO (MARTIN)' => 'NAM',
		'MARIANNE'         => 'NỮ',
		
		'ANNA MARIA CLARA' => 'NỮ',
		'MA.TERESA'        => 'NỮ',
		'GIUSE-MARIA'      => 'NAM',
		
		'M.MARIE' => 'NỮ',
		'SILAO'   => 'NAM',
		
		'MARIA-GIUSE'   => 'NỮ',
		'MARIA-AGATA'   => 'NỮ',
		'MARIA-TERESA'  => 'NỮ',
		'GIUSE-GIERADO' => 'NAM',
		
		'MAGARITA'                => 'NỮ',
		'MARIA-GORETTI'           => 'NỮ',
		'VINH-SƠN (VINCENTE)'     => 'NAM',
		'CATARINA'                => 'NỮ',
		'TOMA'                    => 'NAM',
		'MICAE'                   => 'NAM',
		'ANTON'                   => 'NAM',
		'ĐA-MINH (DAMINH)'        => 'NAM',
		'GIOAN-BAOTIXITA'         => 'NAM',
		'GIOAN-KIM'               => 'NAM',
		'FAUSTINA'                => 'NỮ',
		'AUGUSTINO'               => 'NAM',
		'MÁC-TA (MACTA)'          => 'NỮ',
		'PHERO ĐA'                => 'NAM',
		'LUCIA'                   => 'NỮ',
		'CECILIA'                 => 'NỮ',
		'GIOAN'                   => 'NAM',
		'AGATA'                   => 'NỮ',
		'PHANXICO'                => 'NAM',
		'PHANXICO-XAVIE'          => 'NAM',
		'PHANXICO-ASSISI'         => 'NAM',
		'GIOAN-KIM-KHẨU'          => 'NAM',
		'PHILIPPHE'               => 'NAM',
		'ELIZABETH'               => 'NỮ',
		'MONICA'                  => 'NỮ',
		'GIERADO (GIÊ-RA-ĐÔ)'     => 'NAM',
		'BENADO (BÊ-NA-ĐÔ)'       => 'NAM',
		'AGNES'                   => 'NỮ',
		'ANPHONGSO (AN-PHÔNG-SÔ)' => 'NAM',
		'STEPHANO'                => 'NAM',
		'ISAVE'                   => 'NỮ',
		'MÁC-CÔ (MARK)'           => 'NAM',
		'EMMANUEL'                => 'NAM',
		
		'ALBERTO'                      => 'NAM',
		'GIOAN-PHAOLO'                 => 'NAM',
		'GIOAN-BOSCO'                  => 'NAM',
		'DAMINH SAVIO (ĐA-MINH-SAVIO)' => 'NAM',
		'PIO (PI-Ô)'                   => 'NAM',
		'M.NELLA'                      => 'NAM',
		'DOMINICO'                     => 'NAM',
		
		'GIACÔBÊ (GIACOBE)' => 'NAM',
		'MÁTTHÊU (MATTHÊU)' => 'NAM'
	];
	
	public static $christianNames = [
		'MÁC-CÔ (MARK)'     => 'Mark',
		'MÁTTHÊU (MATTHÊU)' => 'Matthew',
		'PHERO'             => 'Peter',
		'PHAOLO'            => 'Paul',
		'GIUSE'             => 'Joseph',
		'LUCA'              => 'Luke',
		'ANRE'              => 'Andrew',
		
		'TERESA'            => 'Therese',
		'MARIA'             => 'Mary',
		'ANNA'              => 'Anne',
		'MARIA MADALENA'    => 'Mary Magdalene',
		'MARTINO DE PORRES' => 'Martin de Porres',
		'ROSA LIMA'         => 'ROSA LIMA',
		
		'DM VINCENTE'      => 'DM VINCENTE',
		'MARTINO (MARTIN)' => 'MARTINO',
		'MARIANNE'         => 'MARIANNE',
		
		'ANNA MARIA CLARA' => 'ANNA MARIA CLARA',
		'MA.TERESA'        => 'MA.TERESA',
		'GIUSE-MARIA'      => 'GIUSE-MARIA',
		'M.NELLA'          => 'M.NELLA',
		'M.MARIE'          => 'M.MARIE',
		'SILAO'            => 'SILAO',
		
		'MARIA-GIUSE'  => 'MARIA-GIUSE',
		'MARIA-AGATA'  => 'MARIA-AGATA',
		'MARIA-TERESA' => 'MARIA-TERESA',
		
		'MAGARITA'                     => 'Margarita',
		'MARIA-GORETTI'                => 'Maria Goretti',
		'VINH-SƠN (VINCENTE)'          => 'Vincent',
		'CATARINA'                     => 'Catherine',
		'TOMA'                         => 'Thomas',
		'MICAE'                        => 'Michael',
		'ANTON'                        => 'Anthony',
		'DOMINICO'                     => 'DOMINICO',
		'ĐA-MINH (DAMINH)'             => 'Dominic',
		'GIOAN-BAOTIXITA'              => 'John the Baptist',
		'GIOAN-KIM'                    => 'Joachim',
		'FAUSTINA'                     => 'Faustina',
		'AUGUSTINO'                    => 'Augustine of Hippo',
		'MÁC-TA (MACTA)'               => 'Martha of Bethany',
		'PHERO ĐA'                     => 'Peter Đa',
		'LUCIA'                        => 'Lucy',
		'CECILIA'                      => 'Cecilia',
		'GIOAN'                        => 'John',
		'AGATA'                        => 'Agatha',
		'PHANXICO'                     => 'Francis',
		'PHANXICO-XAVIE'               => 'Francis Xavier',
		'PHANXICO-ASSISI'              => 'Francis of Assisi',
		'GIOAN-KIM-KHẨU'               => 'John Chrysostom',
		'PHILIPPHE'                    => 'Philip',
		'ELIZABETH'                    => 'Elizabeth',
		'MONICA'                       => 'Monica of Hippo',
		'GIUSE-GIERADO'                => 'GIUSE-GIERADO',
		'GIERADO (GIÊ-RA-ĐÔ)'          => 'Gerard',
		'BENADO (BÊ-NA-ĐÔ)'            => 'Bernard',
		'AGNES'                        => 'Agnes of Rome',
		'ANPHONGSO (AN-PHÔNG-SÔ)'      => 'Alphonsus Maria de\' Liguori',
		'STEPHANO'                     => 'Stephen',
		'ISAVE'                        => 'Elizabeth (Isave)',
		'EMMANUEL'                     => 'Emmanuel',
		'ALBERTO'                      => 'Albertus',
		'GIOAN-PHAOLO'                 => 'John-Paul',
		'GIOAN-BOSCO'                  => 'John Bosco',
		'DAMINH SAVIO (ĐA-MINH-SAVIO)' => 'Dominic Savio',
		'PIO (PI-Ô)'                   => 'Pius',
		'GIEROMINO'                    => 'Jerome',
		'NI-CÔ-LA (SANTA CLAUS)'       => 'Nicholas',
		'GIACÔBÊ (GIACOBE)'            => 'James'
	
	];
	
	const PHAN_DOAN_CHIEN = 'PHAN_DOAN_CHIEN';
	const PHAN_DOAN_AU = 'PHAN_DOAN_AU';
	const PHAN_DOAN_THIEU = 'PHAN_DOAN_THIEU';
	const PHAN_DOAN_NGHIA = 'PHAN_DOAN_NGHIA';
	const PHAN_DOAN_TONG_DO = 'PHAN_DOAN_TONG_DO';
	const DU_TRUONG = 'DU_TRUONG';
	const HUYNH_TRUONG = 'HUYNH_TRUONG';
	
	public static $danhSachPhanDoan = [
		'CHIÊN CON'  => self::PHAN_DOAN_CHIEN,
		'ĐOÀN ẤU'    => self::PHAN_DOAN_AU,
		'ĐOÀN THIẾU' => self::PHAN_DOAN_THIEU,
		'NGHĨA SĨ'   => self::PHAN_DOAN_NGHIA,
		'TÔNG ĐỒ'    => self::PHAN_DOAN_TONG_DO,
	];
	
	public static $danhSachChiDoan = [
		4  => self::PHAN_DOAN_CHIEN,
		5  => self::PHAN_DOAN_CHIEN,
		6  => self::PHAN_DOAN_CHIEN,
		7  => self::PHAN_DOAN_AU,
		8  => self::PHAN_DOAN_AU,
		9  => self::PHAN_DOAN_AU,
		10 => self::PHAN_DOAN_THIEU,
		11 => self::PHAN_DOAN_THIEU,
		12 => self::PHAN_DOAN_THIEU,
		13 => self::PHAN_DOAN_NGHIA,
		14 => self::PHAN_DOAN_NGHIA,
		15 => self::PHAN_DOAN_NGHIA,
		16 => self::PHAN_DOAN_TONG_DO,
		17 => self::PHAN_DOAN_TONG_DO,
		18 => self::PHAN_DOAN_TONG_DO,
		19 => self::DU_TRUONG,
		20 => self::HUYNH_TRUONG,
	];
	
	public static function getDanhSachChiDoanTheoPhanDoan($phanDoan = null) {
		$cd = [];
		if( ! empty($phanDoan)) {
			foreach(self::$danhSachChiDoan as $number => $pd) {
				if($phanDoan === $pd) {
					$cd [] = $number;
				}
			}
		} else {
			foreach(self::$danhSachChiDoan as $number => $pd) {
				if( ! is_array($cd [ $pd ])) {
					$cd [ $pd ] = [];
				}
				$cd [ $pd ][] = $number;
			}
		}
		
		return $cd;
	}
	
	public function isBanQuanTri() {
		return $this->isBQT();
	}
	
	public function isBQT() {
		return $this->xuDoanPhoNgoai || $this->xuDoanPhoNoi || $this->xuDoanTruong || $this->thuKyXuDoan;
	}
	
	function __invoke() {
	
	}
	
	/** @var ThuKyChiDoan */
	protected $thuKyChiDoanObj;
	/** @var HuynhTruong */
	protected $huynhTruongObj;
	/** @var ChiDoan */
	protected $chiDoanObj;
	/** @var PhanDoanTruong */
	protected $phanDoanTruongObj;
	
	private static $booleanObjects = [
		'thuKyChiDoan'   => ThuKyChiDoan::class,
		'huynhTruong'    => HuynhTruong::class,
		'chiDoanTruong'  => ChiDoanTruong::class,
		'phanDoanTruong' => PhanDoanTruong::class,
		'banQuanTri'     => BanQuanTri::class
	];
	
	private function getBooleanObj($prop) {
		if(in_array($prop, array_keys(self::$booleanObjects))) {
			$isProp = 'is' . ucfirst($prop);
			if(empty($this->$isProp())) {
				return null;
			}
			$propObj = $prop . 'Obj';
			if(empty($this->$propObj)) {
				$propClass      = self::$booleanObjects[ $prop ];
				$this->$propObj = new $propClass;
				$this->$propObj->setThanhVien($this);
				$this->$propObj->setPhanBo($this->getPhanBoNamNay());
			}
			
			return $this->$propObj;
		}
		
		return null;
	}
	
	/**
	 * @return BanQuanTri
	 */
	public function getBanQuanTriObj() {
		return $this->getBooleanObj('banQuanTri');
	}
	
	/**
	 * @return PhanDoanTruong
	 */
	public function getPhanDoanTruongObj() {
		return $this->getBooleanObj('phanDoanTruong');
	}
	
	/**
	 * @return ChiDoanTruong
	 */
	public function getChiDoanTruongObj() {
		return $this->getBooleanObj('chiDoanTruong');
	}
	
	/**
	 * @return ThuKyChiDoan
	 */
	public function getThuKyChiDoanObj() {
		return $this->getBooleanObj('thuKyChiDoan');
	}
	
	/**
	 * @return HuynhTruong
	 */
	public function getHuynhTruongObj() {
		return $this->getBooleanObj('huynhTruong');
	}
	
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer",options={"unsigned":true})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=4, nullable=true)
	 */
	protected $code;
	
	
	/**
	 * @var string
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $notes;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=6, nullable=true)
	 */
	protected $sex;
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	public function __construct() {
		$this->phanBoHangNam = new ArrayCollection();
	}
	
	public function isGranted($name, $action = null, ThanhVien $object = null) {
		$name      = strtoupper($name);
		$thanhVien = $this;
		if( ! empty($action)) {
			$action = strtolower($action);
		}
		if(in_array($action, [ 'truong-chi-doan', 'list-thieu-nhi-nhom' ]) || $name === 'EDIT') {
			if($action === 'truong-chi-doan') {
				if( ! empty($thanhVien->getPhanBoNamNay()->isChiDoanTruong())) {
					if($name === 'EDIT') {
						if(empty($object)) {
							return false;
						}
						
						return ($object->getPhanBoNamNay()->getChiDoan() === $thanhVien->getPhanBoNamNay()->getChiDoan());
					}
					
					return true;
				}
				
				return false;
			} elseif($action === 'list-thieu-nhi-nhom') {
				if($name === 'EXPORT') {
					return true;
				}
				
				if($name === 'EDIT') {
					if(empty($object)) {
						return false;
					}
					
					$doiNhomGiaoLy = $object->getPhanBoNamNay()->getDoiNhomGiaoLy();
					
					if(empty($doiNhomGiaoLy)) {
						return false;
					}
					
					$cacTruongPT = $doiNhomGiaoLy->getCacTruongPhuTrachDoi();
					/** @var TruongPhuTrachDoi $item */
					foreach($cacTruongPT as $item) {
						if($item->getPhanBoHangNam()->getThanhVien()->getId() === $thanhVien->getId()) {
							return true;
						}
					}
					
					return false;
				}
				
			}
		}
		
	}
	
	
	/**
	 * @return bool
	 */
	public function isPhanDoanTruongOrSoeur(ThanhVien $tv = null) {
		$isPDTorSoeur = $this->phanDoanTruong || $this->soeur;
		if(empty($tv)) {
			return $isPDTorSoeur;
		}
		
		if($isPDTorSoeur) {
			if( ! empty($tv)) {
				if($this->getPhanDoan() === $tv->getPhanDoan()) {
					return true;
				} else {
					return false;
				}
			}
		}
	}
	
	public function isTruongPTorGreater(ThanhVien $thanhVien) {
		if($this->isCDTorGreater($thanhVien)) {
			return true;
		}
		$phanBo    = $this->getPhanBoNamNay();
		$cacTruong = $phanBo->getCacTruongPhuTrachDoi();
		/** @var TruongPhuTrachDoi $truong */
		foreach($cacTruong as $truong) {
			$phanBoTN = $thanhVien->getPhanBoNamNay();
			if(empty($truong->getDoiNhomGiaoLy()) || empty($phanBoTN) || empty($phanBoTN->getDoiNhomGiaoLy())) {
				return false;
			}
			if($truong->getDoiNhomGiaoLy()->getId() === $phanBoTN->getDoiNhomGiaoLy()->getId()) {
				return true;
			}
		}
		
		return null;
	}
	
	public function isCDTorGreater(ThanhVien $thanhVien = null) {
		if( ! $this->isHuynhTruong()) {
			return false;
		}
		
		if($this->isBQT()) {
			return true;
		}
		
		if($this->isPhanDoanTruongOrSoeur()) {
			return true;
		}
		if( ! empty($thanhVien)) {
			if($this->getChiDoan() !== $thanhVien->getChiDoan()) {
				return false;
			}
		}
		
		if($this->isChiDoanTruong()) {
			return true;
		}
		
		return false;
	}
	
	public function sanhHoatLai(NamHoc $namHoc) {
		if($this->enabled) {
			return true;
		}
		$this->enabled = true;
		$this->setNamHoc($namHoc->getId());
		
		return $this->initiatePhanBo($namHoc);
	}
	
	public function getPhanBoSauCung() {
		$namHoc         = 0;
		$phanBoCuoiCung = null;
		/** @var PhanBo $phanBo */
		foreach($this->phanBoHangNam as $phanBo) {
			if(($_namHocSau = $phanBo->getNamHoc()->getId()) > $namHoc) {
				$namHoc         = $_namHocSau;
				$phanBoCuoiCung = $phanBo;
			}
		}
		
		return $phanBoCuoiCung;
	}
	
	/**
	 * @return PhanBo|null
	 */
	public function getPhanBoNamNay() {
		/** @var PhanBo $phanBo */
		foreach($this->phanBoHangNam as $phanBo) {
			if( ! empty($namHoc = $phanBo->getNamHoc())) {
				if($namHoc->isStarted() && $namHoc->isEnabled()) {
					$namHoc->getNamHocHienTai();
					
					return $phanBo;
				}
			}
//			if( ! empty($chiDoan = $phanBo->getChiDoan())) {
//				if($chiDoan->getNamHoc()->isEnabled()) {
//					return $phanBo;
//				}
//			}
		}
		
		return null;
	}
	
	public function timPhanBoNamHoc(NamHoc $namHoc) {
		/** @var PhanBo $phanBo */
		foreach($this->phanBoHangNam as $phanBo) {
			if($phanBo->getNamHoc()->getId() === $namHoc->getId()) {
				return $phanBo;
			}
		}
		
		return null;
	}
	
	public function initiatePhanBo(NamHoc $namHoc) {
		$phanBo = null;
		if( ! $this->enabled) {
			return $phanBo;
		}
		
		if(empty($phanBo = $this->timPhanBoNamHoc($namHoc))) {
			$phanBoMoi = new PhanBo();
			$phanBoMoi->setThanhVien($this);
			$phanBoMoi->setPhanDoan($this->phanDoan);
			if( ! $this->isHuynhTruong()) {
				$phanBoMoi->createBangDiem();
			}
			$this->phanBoHangNam->add($phanBoMoi);
			
			$newCDNumber = $this->chiDoan;
			
			if( ! empty($newCDNumber)) {
				$chiDoanMoi = $namHoc->getChiDoanWithNumber($newCDNumber);
				$phanBoMoi->setChiDoan($chiDoanMoi);
				$chiDoanMoi->getPhanBoHangNam()->add($phanBoMoi);
			}
			$phanBoMoi->setVaiTro();
			$phanBoMoi->setNamHoc($namHoc);
			$namHoc->getPhanBoHangNam()->add($phanBoMoi);
			
			return $phanBoMoi;
		} else {
			$phanBo->setVaiTro();
			$phanBo->setPhanDoan($this->phanDoan);
			
			$newCDNumber = $this->chiDoan;
			$chiDoanCu   = $phanBo->getChiDoan();
			
			if(empty($newCDNumber)) {
				if( ! empty($chiDoanCu)) {
					// Learning Point: switch these 2 lines and you will see loll
					$chiDoanCu->getPhanBoHangNam()->removeElement($phanBo);
					$phanBo->setChiDoan(null);
				}
			} elseif( ! empty($newCDNumber) && (empty($chiDoanCu) || ! empty($chiDoanCu) && $newCDNumber !== $chiDoanCu->getNumber())) {
				$chiDoanMoi = $namHoc->getChiDoanWithNumber($newCDNumber);
				if( ! empty($chiDoanCu)) {
					$chiDoanCu->getPhanBoHangNam()->removeElement($phanBo);
				}
				
				$phanBo->setChiDoan($chiDoanMoi);
				$chiDoanMoi->getPhanBoHangNam()->add($phanBo);
			}
			
			return $phanBo;
		}
		
		
		return $phanBo;
	}
	
	/**
	 * @param NamHoc $namHoc
	 *
	 * @return PhanBo|bool
	 */
	public function chuyenNhom(NamHoc $namHoc) {
		if(empty($this->isThieuNhi())) {
			return false;
		}
		$namCu    = $namHoc->getId() - 1;
		$namHocCu = null;
		$phanBoCu = null;
		
		$dsPhanBo = $this->phanBoHangNam;
		/** @var PhanBo $phanBo */
		foreach($dsPhanBo as $phanBo) {
			$chiDoan = $phanBo->getChiDoan();
			$_namHoc = $chiDoan->getNamHoc();
			if($_namHoc->getId() === $namCu) {
				$namHocCu = $_namHoc;
				$phanBoCu = $phanBo;
			} elseif($_namHoc->getId() === $namHoc) {
				return false;
			}
		}
		
		$bangDiemCu  = $phanBoCu->getBangDiem();
		$oldCDNumber = $phanBoCu->getChiDoan()->getNumber();
		if($bangDiemCu->isGradeRetention()) {
//			$phanBoCu->setChiDoan($namHoc->getChiDoanWithNumber($oldCDNumber));
			$newCDNumber = $oldCDNumber;
		} else {
			$newCDNumber = $oldCDNumber + 1;
		}
		
		$phanBoMoi = new PhanBo();
		$phanBoMoi->setThanhVien($this);
		$this->phanBoHangNam->add($phanBoMoi);
		
		$chiDoanMoi = $namHoc->getChiDoanWithNumber($newCDNumber);
		$phanBoMoi->setChiDoan($chiDoanMoi);
		$chiDoanMoi->getPhanBoHangNam()->add($phanBoMoi);
		
		$phanBoMoi->setNamHoc($namHoc);
		$namHoc->getPhanBoHangNam()->add($phanBoMoi);
		$phanBoMoi->setThieuNhi(true);
		$phanBoMoi->setPhanBoTruoc($phanBoCu);
		$phanBoCu->setPhanBoSau($phanBoMoi);
		
		$this->setChiDoan($newCDNumber);
		$this->setPhanDoan($phanBoMoi->getPhanDoan());
		$this->setNamHoc($namHoc->getId());
		
		return $phanBoMoi;
	}
	
	public function getTitle() {
		if($this->sex === 'NAM') {
			return 'anh';
		} elseif($this->sex === 'NỮ') {
			return 'chị';
		}
	}
	
	/**
	 * @var User
	 * @ORM\OneToOne(targetEntity="App\Entity\User\User", inversedBy="thanhVien", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_user", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $user;
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\PhanBo", mappedBy="thanhVien", cascade={"persist","merge"}, orphanRemoval=true)
	 */
	protected $phanBoHangNam;
	
	/**
	 * @var ChristianName
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\ChristianName", inversedBy="cacThanhVien", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_ten_thanh", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $tenThanh;
	
	/**
	 * @var ChristianName
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\ChristianName", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_ten_thanh_bo", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $tenThanhBo;
	
	/**
	 * @var ChristianName
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\ChristianName", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_ten_thanh_me", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $tenThanhMe;
	
	/**
	 * @var ChristianName
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\ChristianName", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_ten_giao_khu", referencedColumnName="id", onDelete="SET NULL")
	 */
	protected $tenGiaoKhu;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $dob;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $soAnhChiEm = 0;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $namHoc;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $chiDoan;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $soeur = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":true})
	 */
	protected $enabled = true;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $ngheoKho = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $dacBiet = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $approved = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duBi = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $thieuNhi = true;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $duTruong = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $huynhTruong = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $chiDoanTruong = false;
	
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $thuKyChiDoan = false;
	
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $phanDoanTruong = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $xuDoanTruong = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $xuDoanPhoNoi = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $xuDoanPhoNgoai = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $thuKyXuDoan = false;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $thuQuyXuDoan = false;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $soDienThoai;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $soDienThoaiSecours;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $soDienThoaiMe;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $soDienThoaiBo;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $soDienThoaiNha;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $hoTenBo;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $hoTenMe;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $ngheNghiepBo;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $ngheNghiepMe;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $diaChiThuongTru;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $diaChiTamTru;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $phanDoan;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=512)
	 */
	protected $quickName;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=512)
	 */
	protected $name;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=255)
	 */
	protected $firstname;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=255)
	 */
	protected $middlename;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=255)
	 */
	protected $lastname;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true,length=255)
	 */
	protected $christianname;
	
	/**
	 * @return int
	 */
	public function getChiDoan() {
		return $this->chiDoan;
	}
	
	/**
	 * @param int $chiDoan
	 */
	public function setChiDoan($chiDoan) {
		$this->chiDoan = $chiDoan;
	}
	
	/**
	 * @return bool
	 */
	public function isDuBi() {
		return $this->duBi;
	}
	
	/**
	 * @param bool $duBi
	 */
	public function setDuBi($duBi) {
		$this->duBi = $duBi;
	}
	
	/**
	 * @return bool
	 */
	public function isDuTruong() {
		return $this->duTruong;
	}
	
	/**
	 * @param bool $duTruong
	 */
	public function setDuTruong($duTruong) {
		$this->duTruong = $duTruong;
	}
	
	/**
	 * @return bool
	 */
	public function isHuynhTruong() {
		return $this->huynhTruong;
	}
	
	/**
	 * @param bool $huynhTruong
	 */
	public function setHuynhTruong($huynhTruong) {
		$this->huynhTruong = $huynhTruong;
	}
	
	/**
	 * @return bool
	 */
	public function isChiDoanTruong() {
		return $this->chiDoanTruong;
	}
	
	/**
	 * @param bool $chiDoanTruong
	 */
	public function setChiDoanTruong($chiDoanTruong) {
		$this->chiDoanTruong = $chiDoanTruong;
	}
	
	/**
	 * @return string
	 */
	public function getFirstname() {
		return $this->firstname;
	}
	
	/**
	 * @param string $firstname
	 */
	public function setFirstname($firstname) {
		$this->firstname = $firstname;
	}
	
	/**
	 * @return string
	 */
	public function getChristianname() {
		return $this->christianname;
	}
	
	/**
	 * @param string $christianname
	 */
	public function setChristianname($christianname) {
		$this->christianname = $christianname;
	}
	
	/**
	 * @return bool
	 */
	public function isThieuNhi() {
		return $this->thieuNhi;
	}
	
	/**
	 * @param bool $thieuNhi
	 */
	public function setThieuNhi($thieuNhi) {
		$this->thieuNhi = $thieuNhi;
	}
	
	/**
	 * @return bool
	 */
	public function isPhanDoanTruong() {
		return $this->phanDoanTruong;
	}
	
	/**
	 * @param bool $phanDoanTruong
	 */
	public function setPhanDoanTruong($phanDoanTruong) {
		$this->phanDoanTruong = $phanDoanTruong;
	}
	
	/**
	 * @return bool
	 */
	public function isXuDoanTruong() {
		return $this->xuDoanTruong;
	}
	
	/**
	 * @param bool $xuDoanTruong
	 */
	public function setXuDoanTruong($xuDoanTruong) {
		$this->xuDoanTruong = $xuDoanTruong;
	}
	
	/**
	 * @return bool
	 */
	public function isXuDoanPhoNoi() {
		return $this->xuDoanPhoNoi;
	}
	
	/**
	 * @param bool $xuDoanPhoNoi
	 */
	public function setXuDoanPhoNoi($xuDoanPhoNoi) {
		$this->xuDoanPhoNoi = $xuDoanPhoNoi;
	}
	
	/**
	 * @return bool
	 */
	public function isXuDoanPhoNgoai() {
		return $this->xuDoanPhoNgoai;
	}
	
	/**
	 * @param bool $xuDoanPhoNgoai
	 */
	public function setXuDoanPhoNgoai($xuDoanPhoNgoai) {
		$this->xuDoanPhoNgoai = $xuDoanPhoNgoai;
	}
	
	/**
	 * @return string
	 */
	public function getPhanDoan() {
		return $this->phanDoan;
	}
	
	/**
	 * @param string $phanDoan
	 */
	public function setPhanDoan($phanDoan) {
		$this->phanDoan = $phanDoan;
	}
	
	/**
	 * @return string
	 */
	public function getMiddlename() {
		return $this->middlename;
	}
	
	/**
	 * @param string $middlename
	 */
	public function setMiddlename($middlename) {
		$this->middlename = $middlename;
	}
	
	/**
	 * @return string
	 */
	public function getLastname() {
		return $this->lastname;
	}
	
	/**
	 * @param string $lastname
	 */
	public function setLastname($lastname) {
		$this->lastname = $lastname;
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
	 * @return string
	 */
	public function getSoDienThoai() {
		return $this->soDienThoai;
	}
	
	/**
	 * @param string $soDienThoai
	 */
	public function setSoDienThoai($soDienThoai) {
		$this->soDienThoai = $soDienThoai;
	}
	
	/**
	 * @return string
	 */
	public function getSoDienThoaiMe() {
		return $this->soDienThoaiMe;
	}
	
	/**
	 * @param string $soDienThoaiMe
	 */
	public function setSoDienThoaiMe($soDienThoaiMe) {
		$this->soDienThoaiMe = $soDienThoaiMe;
	}
	
	/**
	 * @return string
	 */
	public function getSoDienThoaiBo() {
		return $this->soDienThoaiBo;
	}
	
	/**
	 * @param string $soDienThoaiBo
	 */
	public function setSoDienThoaiBo($soDienThoaiBo) {
		$this->soDienThoaiBo = $soDienThoaiBo;
	}
	
	/**
	 * @return string
	 */
	public function getSoDienThoaiNha() {
		return $this->soDienThoaiNha;
	}
	
	/**
	 * @param string $soDienThoaiNha
	 */
	public function setSoDienThoaiNha($soDienThoaiNha) {
		$this->soDienThoaiNha = $soDienThoaiNha;
	}
	
	/**
	 * @return string
	 */
	public function getDiaChiThuongTru() {
		return $this->diaChiThuongTru;
	}
	
	/**
	 * @param string $diaChiThuongTru
	 */
	public function setDiaChiThuongTru($diaChiThuongTru) {
		$this->diaChiThuongTru = $diaChiThuongTru;
	}
	
	/**
	 * @return string
	 */
	public function getDiaChiTamTru() {
		return $this->diaChiTamTru;
	}
	
	/**
	 * @param string $diaChiTamTru
	 */
	public function setDiaChiTamTru($diaChiTamTru) {
		$this->diaChiTamTru = $diaChiTamTru;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDob() {
		return $this->dob;
	}
	
	/**
	 * @param \DateTime $dob
	 */
	public function setDob($dob) {
		$this->dob = $dob;
	}
	
	/**
	 * @return int
	 */
	public function getNamHoc() {
		return $this->namHoc;
	}
	
	/**
	 * @param int $namHoc
	 */
	public function setNamHoc($namHoc) {
		$this->namHoc = $namHoc;
	}
	
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	
	/**
	 * @return string
	 */
	public function getQuickName() {
		return $this->quickName;
	}
	
	/**
	 * @param string $quickName
	 */
	public function setQuickName($quickName) {
		$this->quickName = $quickName;
	}
	
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
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * @param User $user
	 */
	public function setUser($user) {
		$this->user = $user;
	}
	
	/**
	 * @return bool
	 */
	public function isApproved() {
		return $this->approved;
	}
	
	/**
	 * @param bool $approved
	 */
	public function setApproved($approved) {
		$this->approved = $approved;
	}
	
	/**
	 * @return mixed
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @param mixed $code
	 */
	public function setCode($code) {
		$this->code = $code;
	}
	
	/**
	 * @return string
	 */
	public function getSex() {
		return $this->sex;
	}
	
	/**
	 * @param string $sex
	 */
	public function setSex($sex) {
		$this->sex = $sex;
	}
	
	/**
	 * @return string
	 */
	public function getSoDienThoaiSecours() {
		return $this->soDienThoaiSecours;
	}
	
	/**
	 * @param string $soDienThoaiSecours
	 */
	public function setSoDienThoaiSecours($soDienThoaiSecours) {
		$this->soDienThoaiSecours = $soDienThoaiSecours;
	}
	
	/**
	 * @return bool
	 */
	public function isThuKyXuDoan() {
		return $this->thuKyXuDoan;
	}
	
	/**
	 * @param bool $thuKyXuDoan
	 */
	public function setThuKyXuDoan($thuKyXuDoan) {
		$this->thuKyXuDoan = $thuKyXuDoan;
	}
	
	/**
	 * @return ChristianName
	 */
	public function getTenThanh() {
		return $this->tenThanh;
	}
	
	/**
	 * @param ChristianName $tenThanh
	 */
	public function setTenThanh($tenThanh) {
		$this->tenThanh = $tenThanh;
	}
	
	/**
	 * @return string
	 */
	public function getHoTenBo() {
		return $this->hoTenBo;
	}
	
	/**
	 * @param string $hoTenBo
	 */
	public function setHoTenBo($hoTenBo) {
		$this->hoTenBo = $hoTenBo;
	}
	
	/**
	 * @return string
	 */
	public function getHoTenMe() {
		return $this->hoTenMe;
	}
	
	/**
	 * @param string $hoTenMe
	 */
	public function setHoTenMe($hoTenMe) {
		$this->hoTenMe = $hoTenMe;
	}
	
	/**
	 * @return ChristianName
	 */
	public function getTenGiaoKhu() {
		return $this->tenGiaoKhu;
	}
	
	/**
	 * @param ChristianName $tenGiaoKhu
	 */
	public function setTenGiaoKhu($tenGiaoKhu) {
		$this->tenGiaoKhu = $tenGiaoKhu;
	}
	
	/**
	 * @return array
	 */
	public static function getChristianNameSex() {
		return self::$christianNameSex;
	}
	
	/**
	 * @param array $christianNameSex
	 */
	public static function setChristianNameSex($christianNameSex) {
		self::$christianNameSex = $christianNameSex;
	}
	
	/**
	 * @return string
	 */
	public function getNotes() {
		return $this->notes;
	}
	
	/**
	 * @param string $notes
	 */
	public function setNotes($notes) {
		$this->notes = $notes;
	}
	
	/**
	 * @return bool
	 */
	public function isThuQuyXuDoan() {
		return $this->thuQuyXuDoan;
	}
	
	/**
	 * @param bool $thuQuyXuDoan
	 */
	public function setThuQuyXuDoan($thuQuyXuDoan) {
		$this->thuQuyXuDoan = $thuQuyXuDoan;
	}
	
	/**
	 * @return string
	 */
	public function getNgheNghiepBo() {
		return $this->ngheNghiepBo;
	}
	
	/**
	 * @param string $ngheNghiepBo
	 */
	public function setNgheNghiepBo($ngheNghiepBo) {
		$this->ngheNghiepBo = $ngheNghiepBo;
	}
	
	/**
	 * @return string
	 */
	public function getNgheNghiepMe() {
		return $this->ngheNghiepMe;
	}
	
	/**
	 * @param string $ngheNghiepMe
	 */
	public function setNgheNghiepMe($ngheNghiepMe) {
		$this->ngheNghiepMe = $ngheNghiepMe;
	}
	
	/**
	 * @return ChristianName
	 */
	public function getTenThanhBo() {
		return $this->tenThanhBo;
	}
	
	/**
	 * @param ChristianName $tenThanhBo
	 */
	public function setTenThanhBo($tenThanhBo) {
		$this->tenThanhBo = $tenThanhBo;
	}
	
	/**
	 * @return ChristianName
	 */
	public function getTenThanhMe() {
		return $this->tenThanhMe;
	}
	
	/**
	 * @param ChristianName $tenThanhMe
	 */
	public function setTenThanhMe($tenThanhMe) {
		$this->tenThanhMe = $tenThanhMe;
	}
	
	/**
	 * @return int
	 */
	public function getSoAnhChiEm() {
		return $this->soAnhChiEm;
	}
	
	/**
	 * @param int $soAnhChiEm
	 */
	public function setSoAnhChiEm($soAnhChiEm) {
		$this->soAnhChiEm = $soAnhChiEm;
	}
	
	/**
	 * @return bool
	 */
	public function isNgheoKho() {
		return $this->ngheoKho;
	}
	
	/**
	 * @param bool $ngheoKho
	 */
	public function setNgheoKho($ngheoKho) {
		$this->ngheoKho = $ngheoKho;
	}
	
	/**
	 * @return bool
	 */
	public function isDacBiet() {
		return $this->dacBiet;
	}
	
	/**
	 * @param bool $dacBiet
	 */
	public function setDacBiet($dacBiet) {
		$this->dacBiet = $dacBiet;
	}
	
	/**
	 * @return bool
	 */
	public function isSoeur() {
		return $this->soeur;
	}
	
	/**
	 * @param bool $soeur
	 */
	public function setSoeur($soeur) {
		$this->soeur = $soeur;
	}
	
	/**
	 * @return bool
	 */
	public function isThuKyChiDoan() {
		return $this->thuKyChiDoan;
	}
	
	/**
	 * @param bool $thuKyChiDoan
	 */
	public function setThuKyChiDoan($thuKyChiDoan) {
		$this->thuKyChiDoan = $thuKyChiDoan;
	}
	
}