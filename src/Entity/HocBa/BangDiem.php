<?php

namespace App\Entity\HocBa;

use App\Entity\HoSo\PhanBo;
use App\Entity\User\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hocba__bang_diem")
 */
class BangDiem {
	const YEU = 'YEU';
	const TRUNG_BINH = 'TRUNG_BINH';
	const KHA = 'KHA';
	const GIOI = 'GIOI';
	
	public function getCategoryTrans() {
		if($this->isGradeRetention()) {
			return 'Ở LẠI';
		}
		if($this->category === self::YEU) {
			return 'Ở LẠI';
		}
		if($this->category === self::TRUNG_BINH) {
			return 'TRUNG BÌNH';
		}
		if($this->category === self::KHA) {
			return 'KHÁ';
		}
		if($this->category === self::GIOI) {
			return 'GIỎI';
		}
	}
	
	/**
	 * ID_REF
	 * @ORM\Id
	 * @ORM\Column(type="string", length=24)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="App\Doctrine\ORM\RandomIdGenerator")
	 */
	protected $id;
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	public function tinhDiemChuyenCan($hocKy = 1) {
		$cacCotDiemBiLoaiBo = $this->phanBo->getChiDoan()->getCotDiemBiLoaiBo();
		$cols               = null;
		$tbCC               = 0;
		if($hocKy === 1) {
			$cols = [ 'cc9' => 'cc9', 'cc10' => 'cc10', 'cc11' => 'cc11', 'cc12' => 'cc12' ];
		} elseif($hocKy === 2) {
			$cols = [ 'cc1' => 'cc1', 'cc2' => 'cc2', 'cc3' => 'cc3', 'cc4' => 'cc4', 'cc5' => 'cc5' ];
		} else {
			return null;
		}
		
		if(is_array($cacCotDiemBiLoaiBo)) {
			foreach($cacCotDiemBiLoaiBo as $cotDiemBiLoaiBo) {
				unset($cols[ $cotDiemBiLoaiBo ]);
			}
		}
		
		$colCount = count($cols);
		$sumCC    = 0;
		foreach($cols as $col) {
			if(empty($_cc = $this->$col)) {
				continue;
			}
			$sumCC += $_cc;
		}
		
		if($hocKy === 1) {
			$tbCC = $this->tbCCTerm1 = $sumCC / $colCount;
		} elseif($hocKy === 2) {
			$tbCC = $this->tbCCTerm2 = $sumCC / $colCount;
		}
		
		
		return $tbCC;
	}
	
	public function tinhDiemGiaoLy($hocKy = 1) {
		$cacCotDiemBiLoaiBo = $this->phanBo->getChiDoan()->getCotDiemBiLoaiBo();
		$cols               = null;
		$tbGL               = 0;
		if($hocKy === 1) {
			$cols = [
				'quizTerm1' => 'quizTerm1',
				'midTerm1'  => 'midTerm1',
				
				'finalTerm1-1' => 'finalTerm1',
				'finalTerm1-2' => 'finalTerm1'
			];
		} elseif($hocKy === 2) {
			$cols = [
				'quizTerm2'    => 'quizTerm2',
				'midTerm2'     => 'midTerm2',
				'finalTerm2-1' => 'finalTerm2',
				'finalTerm2-2' => 'finalTerm2'
			];
		} else {
			return null;
		}
		
		if(is_array($cacCotDiemBiLoaiBo)) {
			foreach($cacCotDiemBiLoaiBo as $cotDiemBiLoaiBo) {
				unset($cols[ $cotDiemBiLoaiBo ]);
			}
		}
		
		$colCount = count($cols);
		$sumGL    = 0;
		foreach($cols as $col) {
			if(empty($_gl = $this->$col)) {
				continue;
			}
			$sumGL += $_gl;
		}
		
		if($hocKy === 1) {
			$tbGL = $this->tbGLTerm1 = $sumGL / $colCount;
		} elseif($hocKy === 2) {
			$tbGL = $this->tbGLTerm2 = $sumGL / $colCount;
		}
		
		
		return $tbGL;
	}
	
	public function tinhDiemHocKy($hocKy = 1) {
		$cacCotDiemBiLoaiBo = $this->phanBo->getChiDoan()->getCotDiemBiLoaiBo();
		$cols               = null;
		
		if($hocKy === 1) {
			$cols = [
				'tbCCTerm1' => 'tbCCTerm1',
				'quizTerm1' => 'quizTerm1',
				'midTerm1'  => 'midTerm1',
				
				'finalTerm1-1' => 'finalTerm1',
				'finalTerm1-2' => 'finalTerm1'
			];
		} elseif($hocKy === 2) {
			$cols = [
				'tbCCTerm2' => 'tbCCTerm2',
				'quizTerm2' => 'quizTerm2',
				'midTerm2'  => 'midTerm2',
				
				'finalTerm2-1' => 'finalTerm2',
				'finalTerm2-2' => 'finalTerm2'
			];
		} else {
			return null;
		}
		
		if(is_array($cacCotDiemBiLoaiBo)) {
			foreach($cacCotDiemBiLoaiBo as $cotDiemBiLoaiBo) {
				if($cotDiemBiLoaiBo === 'finalTerm1') {
					unset($cols['finalTerm1-1']);
					unset($cols['finalTerm1-2']);
				} elseif($cotDiemBiLoaiBo === 'finalTerm2') {
					unset($cols['finalTerm2-1']);
					unset($cols['finalTerm2-2']);
				} else {
					unset($cols[ $cotDiemBiLoaiBo ]);
				}
			}
		}
		
		$colCount = count($cols);
		$sumDiem  = 0;
		foreach($cols as $col) {
			if(empty($_diem = $this->$col)) {
				continue;
			}
			
			$sumDiem += $_diem;
		}
		
		if($hocKy === 1) {
			$tbTerm = $this->tbTerm1 = $sumDiem / $colCount;
			$this->tinhDiemGiaoLy(1);
		} elseif($hocKy === 2) {
			if($this->tbGLTerm1 === null) {
				$this->tinhDiemGiaoLy(1);
			}
			
			$this->tinhDiemGiaoLy(2);
			$this->tbGLYear = ($this->tbGLTerm1 + $this->tbGLTerm2) / 2;
			
			$tbTerm       = $this->tbTerm2 = $sumDiem / $colCount;
			$this->tbYear = ($this->tbTerm1 + $this->tbTerm2) / 2;
			$this->congPhieuChuaNhat();
			
			$namHoc = $this->phanBo->getNamHoc();
			switch(true) {
				case $this->tbYear >= $namHoc->getDiemGioi():
					$this->category = self::GIOI;
					break;
				case $this->tbYear >= $namHoc->getDiemKha():
					$this->category = self::KHA;
					break;
				case $this->tbYear >= $namHoc->getDiemTB():
					$this->category = self::TRUNG_BINH;
					break;
				default:
					$this->category = self::YEU;
					break;
			}
			if(($sundayTickets = $this->sundayTickets) >= $namHoc->getPhieuLenLop() && $this->category !== self::YEU) {
				$this->gradeRetention = false;
			} else {
				$this->gradeRetention = true;
			}
			
			if($sundayTickets >= $namHoc->getPhieuKhenThuong() && $this->tbYear >= $namHoc->getDiemKha()) {
				$this->awarded = true;
			} else {
				$this->awarded = false;
			}
		}
		
		return $tbTerm;
	}
	
	public function congPhieuChuaNhat() {
		if($this->sundayTicketTerm1 === null) {
			$this->sundayTicketTerm1 = 0;
		}
		if($this->sundayTicketTerm2 === null) {
			$this->sundayTicketTerm2 = 0;
		}
		$this->sundayTickets = $this->sundayTicketTerm1 + $this->sundayTicketTerm2;
		
		return $this->sundayTickets;
	}
	
	/**
	 * @var PhanBo
	 * @ORM\OneToOne(targetEntity="App\Entity\HoSo\PhanBo",inversedBy="bangDiem")
	 * @ORM\JoinColumn(name="id_phan_bo", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected
		$phanBo;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc9;
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc10;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc11;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc12;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc3;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc4;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $cc5;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbCCTerm1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $quizTerm1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $midTerm1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $finalTerm1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbGLTerm1;
	
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbTerm1;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbCCTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $quizTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $midTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $finalTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbGLTerm2;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbGLYear;
	
	/**
	 * @var  float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $tbYear;
	
	/**
	 * @var  integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $sundayTicketTerm1;
	
	/**
	 * @var  integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $sundayTicketTerm2;
	
	/**
	 * @var  integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $sundayTickets;
	
	/**
	 * @var  integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $specialTreatmentTarget;
	
	/**
	 * @var  boolean
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $awarded;
	
	
	/**
	 * @var  boolean
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	protected $submitted = false;
	
	/**
	 * @var  boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $specialTreatment = false;
	
	/**
	 * @var  boolean
	 * @ORM\Column(type="boolean", options={"default":false})
	 */
	protected $specialTreatmentApproved = false;
	
	/**
	 * @var  boolean
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $gradeRetention;
	
	/**
	 * @var  string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $category;
	
	/**
	 * @var  string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $remarks;
	
	/**
	 * @return PhanBo
	 */
	public function getPhanBo() {
		return $this->phanBo;
	}
	
	/**
	 * @param PhanBo $phanBo
	 */
	public function setPhanBo($phanBo) {
		$this->phanBo = $phanBo;
	}
	
	/**
	 * @return float
	 */
	public function getCc9() {
		return $this->cc9;
	}
	
	/**
	 * @param float $cc9
	 */
	public function setCc9($cc9) {
		$this->cc9 = $cc9;
	}
	
	/**
	 * @return float
	 */
	public function getCc10() {
		return $this->cc10;
	}
	
	/**
	 * @param float $cc10
	 */
	public function setCc10($cc10) {
		$this->cc10 = $cc10;
	}
	
	/**
	 * @return float
	 */
	public function getCc11() {
		return $this->cc11;
	}
	
	/**
	 * @param float $cc11
	 */
	public function setCc11($cc11) {
		$this->cc11 = $cc11;
	}
	
	/**
	 * @return float
	 */
	public function getCc12() {
		return $this->cc12;
	}
	
	/**
	 * @param float $cc12
	 */
	public function setCc12($cc12) {
		$this->cc12 = $cc12;
	}
	
	/**
	 * @return float
	 */
	public function getCc1() {
		return $this->cc1;
	}
	
	/**
	 * @param float $cc1
	 */
	public function setCc1($cc1) {
		$this->cc1 = $cc1;
	}
	
	/**
	 * @return float
	 */
	public function getCc2() {
		return $this->cc2;
	}
	
	/**
	 * @param float $cc2
	 */
	public function setCc2($cc2) {
		$this->cc2 = $cc2;
	}
	
	/**
	 * @return float
	 */
	public function getCc3() {
		return $this->cc3;
	}
	
	/**
	 * @param float $cc3
	 */
	public function setCc3($cc3) {
		$this->cc3 = $cc3;
	}
	
	/**
	 * @return float
	 */
	public function getCc4() {
		return $this->cc4;
	}
	
	/**
	 * @param float $cc4
	 */
	public function setCc4($cc4) {
		$this->cc4 = $cc4;
	}
	
	/**
	 * @return float
	 */
	public function getCc5() {
		return $this->cc5;
	}
	
	/**
	 * @param float $cc5
	 */
	public function setCc5($cc5) {
		$this->cc5 = $cc5;
	}
	
	/**
	 * @return float
	 */
	public function getQuizTerm1() {
		return $this->quizTerm1;
	}
	
	/**
	 * @param float $quizTerm1
	 */
	public function setQuizTerm1($quizTerm1) {
		$this->quizTerm1 = $quizTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getMidTerm1() {
		return $this->midTerm1;
	}
	
	/**
	 * @param float $midTerm1
	 */
	public function setMidTerm1($midTerm1) {
		$this->midTerm1 = $midTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getFinalTerm1() {
		return $this->finalTerm1;
	}
	
	/**
	 * @param float $finalTerm1
	 */
	public function setFinalTerm1($finalTerm1) {
		$this->finalTerm1 = $finalTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getQuizTerm2() {
		return $this->quizTerm2;
	}
	
	/**
	 * @param float $quizTerm2
	 */
	public function setQuizTerm2($quizTerm2) {
		$this->quizTerm2 = $quizTerm2;
	}
	
	/**
	 * @return float
	 */
	public function getMidTerm2() {
		return $this->midTerm2;
	}
	
	/**
	 * @param float $midTerm2
	 */
	public function setMidTerm2($midTerm2) {
		$this->midTerm2 = $midTerm2;
	}
	
	/**
	 * @return float
	 */
	public function getFinalTerm2() {
		return $this->finalTerm2;
	}
	
	/**
	 * @param float $finalTerm2
	 */
	public function setFinalTerm2($finalTerm2) {
		$this->finalTerm2 = $finalTerm2;
	}
	
	/**
	 * @return int
	 */
	public function getSundayTicketTerm1() {
		return $this->sundayTicketTerm1;
	}
	
	/**
	 * @param int $sundayTicketTerm1
	 */
	public function setSundayTicketTerm1($sundayTicketTerm1) {
		$this->sundayTicketTerm1 = $sundayTicketTerm1;
	}
	
	/**
	 * @return int
	 */
	public function getSundayTicketTerm2() {
		return $this->sundayTicketTerm2;
	}
	
	/**
	 * @param int $sundayTicketTerm2
	 */
	public function setSundayTicketTerm2($sundayTicketTerm2) {
		$this->sundayTicketTerm2 = $sundayTicketTerm2;
	}
	
	/**
	 * @return int
	 */
	public function getSundayTickets() {
		return $this->sundayTickets;
	}
	
	/**
	 * @param int $sundayTickets
	 */
	public function setSundayTickets($sundayTickets) {
		$this->sundayTickets = $sundayTickets;
	}
	
	/**
	 * @return bool
	 */
	public function isAwarded() {
		return $this->awarded;
	}
	
	/**
	 * @param bool $awarded
	 */
	public function setAwarded($awarded) {
		$this->awarded = $awarded;
	}
	
	/**
	 * @return bool
	 */
	public function isGradeRetention() {
		return $this->gradeRetention;
	}
	
	/**
	 * @param bool $gradeRetention
	 */
	public function setGradeRetention($gradeRetention) {
		$this->gradeRetention = $gradeRetention;
	}
	
	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}
	
	/**
	 * @param string $category
	 */
	public function setCategory($category) {
		$this->category = $category;
	}
	
	/**
	 * @return string
	 */
	public function getRemarks() {
		return $this->remarks;
	}
	
	/**
	 * @param string $remarks
	 */
	public function setRemarks($remarks) {
		$this->remarks = $remarks;
	}
	
	
	/**
	 * @return float
	 */
	public function getTbTerm1() {
		return $this->tbTerm1;
	}
	
	/**
	 * @param float $tbTerm1
	 */
	public function setTbTerm1($tbTerm1) {
		$this->tbTerm1 = $tbTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getTbTerm2() {
		return $this->tbTerm2;
	}
	
	/**
	 * @param float $tbTerm2
	 */
	public function setTbTerm2($tbTerm2) {
		$this->tbTerm2 = $tbTerm2;
	}
	
	/**
	 * @return float
	 */
	public function getTbCCTerm1() {
		return $this->tbCCTerm1;
	}
	
	/**
	 * @param float $tbCCTerm1
	 */
	public function setTbCCTerm1($tbCCTerm1) {
		$this->tbCCTerm1 = $tbCCTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getTbCCTerm2() {
		return $this->tbCCTerm2;
	}
	
	/**
	 * @param float $tbCCTerm2
	 */
	public function setTbCCTerm2($tbCCTerm2) {
		$this->tbCCTerm2 = $tbCCTerm2;
	}
	
	/**
	 * @return float
	 */
	public function getTbYear() {
		return $this->tbYear;
	}
	
	/**
	 * @param float $tbYear
	 */
	public function setTbYear($tbYear) {
		$this->tbYear = $tbYear;
	}
	
	/**
	 * @return bool
	 */
	public function isSubmitted() {
		return $this->submitted;
	}
	
	/**
	 * @param bool $submitted
	 */
	public function setSubmitted($submitted) {
		$this->submitted = $submitted;
	}
	
	/**
	 * @return bool
	 */
	public function isSpecialTreatment() {
		return $this->specialTreatment;
	}
	
	/**
	 * @param bool $specialTreatment
	 */
	public function setSpecialTreatment($specialTreatment) {
		$this->specialTreatment = $specialTreatment;
	}
	
	/**
	 * @return int
	 */
	public function getSpecialTreatmentTarget() {
		return $this->specialTreatmentTarget;
	}
	
	/**
	 * @param int $specialTreatmentTarget
	 */
	public function setSpecialTreatmentTarget($specialTreatmentTarget) {
		$this->specialTreatmentTarget = $specialTreatmentTarget;
	}
	
	/**
	 * @return bool
	 */
	public function isSpecialTreatmentApproved() {
		return $this->specialTreatmentApproved;
	}
	
	/**
	 * @param bool $specialTreatmentApproved
	 */
	public function setSpecialTreatmentApproved($specialTreatmentApproved) {
		$this->specialTreatmentApproved = $specialTreatmentApproved;
	}
	
	/**
	 * @return float
	 */
	public function getTbGLTerm1() {
		return $this->tbGLTerm1;
	}
	
	/**
	 * @param float $tbGLTerm1
	 */
	public function setTbGLTerm1($tbGLTerm1) {
		$this->tbGLTerm1 = $tbGLTerm1;
	}
	
	/**
	 * @return float
	 */
	public function getTbGLTerm2() {
		return $this->tbGLTerm2;
	}
	
	/**
	 * @param float $tbGLTerm2
	 */
	public function setTbGLTerm2($tbGLTerm2) {
		$this->tbGLTerm2 = $tbGLTerm2;
	}
	
	/**
	 * @return float
	 */
	public function getTbGLYear() {
		return $this->tbGLYear;
	}
	
	/**
	 * @param float $tbGLYear
	 */
	public function setTbGLYear($tbGLYear) {
		$this->tbGLYear = $tbGLYear;
	}
	
	
}