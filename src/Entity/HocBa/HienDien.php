<?php
namespace App\Entity\HocBa;

use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="binhle__thieu_nhi__hien_dien")
 */
class HienDien {
	
	const TYPE_THU_5 = 'THU_5';
	const TYPE_CHUA_NHAT = 'CHUA_NHAT';
	
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
	
	function __construct() {
	
	}
	
	/**
	 * @var PhanBo
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\PhanBo", inversedBy="diemDanh")
	 * @ORM\JoinColumn(name="id_phan_bo", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $thieuNhi;
	
	/**
	 * @var PhanBo
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\PhanBo", inversedBy="diemDanhThieuNhi")
	 * @ORM\JoinColumn(name="id_phan_bo", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $huynhTruong;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(type="date", nullable=true)
	 */
	protected $targetDate;
	
	/**
	 * @var \DateTime
	 * @ORM\Column(type="datetime", nullable=true)
	 */
	protected $createdAt;
	
	/**
	 * @var float
	 * @ORM\Column(type="float", nullable=true)
	 */
	protected $score;
	
	/**
	 * @var boolean
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $phieuChuaNhat;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true, length=50)
	 */
	protected $type;
	
	
	/**
	 * @return PhanBo
	 */
	public function getThieuNhi() {
		return $this->thieuNhi;
	}
	
	/**
	 * @param PhanBo $thieuNhi
	 */
	public function setThieuNhi($thieuNhi) {
		$this->thieuNhi = $thieuNhi;
	}
	
	/**
	 * @return PhanBo
	 */
	public function getHuynhTruong() {
		return $this->huynhTruong;
	}
	
	/**
	 * @param PhanBo $huynhTruong
	 */
	public function setHuynhTruong($huynhTruong) {
		$this->huynhTruong = $huynhTruong;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getTargetDate() {
		return $this->targetDate;
	}
	
	/**
	 * @param \DateTime $targetDate
	 */
	public function setTargetDate($targetDate) {
		$this->targetDate = $targetDate;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt() {
		return $this->createdAt;
	}
	
	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt($createdAt) {
		$this->createdAt = $createdAt;
	}
	
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	
	/**
	 * @return float
	 */
	public function getScore() {
		return $this->score;
	}
	
	/**
	 * @param float $score
	 */
	public function setScore($score) {
		$this->score = $score;
	}
	
	/**
	 * @return bool
	 */
	public function isPhieuChuaNhat() {
		return $this->phieuChuaNhat;
	}
	
	/**
	 * @param bool $phieuChuaNhat
	 */
	public function setPhieuChuaNhat($phieuChuaNhat) {
		$this->phieuChuaNhat = $phieuChuaNhat;
	}
}