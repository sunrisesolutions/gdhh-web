<?php

namespace App\Entity\HocBa;

use App\Entity\HoSo\PhanBo;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hocba__hien_dien")
 */
class HienDien {
	
	const TYPE_GIAO_LY = 'GIAO_LY';
	const TYPE_LE_CN = 'LE_CN';
	const TYPE_DIEM_DANH_NONG = 'DIEM_DANH_NONG';
	
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
		$this->createdAt = new \DateTime();
	}
	
	/**
	 * @var PhanBo
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\PhanBo", inversedBy="diemDanh")
	 * @ORM\JoinColumn(name="id_phan_bo_thieu_nhi", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $thieuNhi;
	
	/**
	 * @var PhanBo
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\PhanBo", inversedBy="diemDanhThieuNhi")
	 * @ORM\JoinColumn(name="id_phan_bo_truong", referencedColumnName="id", onDelete="CASCADE")
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
	protected $point;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true, length=250)
	 */
	protected $ghiNhan;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true, length=50)
	 */
	protected $type;
	
	/**
	 * @return PhanBo
	 */
	public function getThieuNhi(): PhanBo {
		return $this->thieuNhi;
	}
	
	/**
	 * @param PhanBo $thieuNhi
	 */
	public function setThieuNhi(PhanBo $thieuNhi): void {
		$this->thieuNhi = $thieuNhi;
	}
	
	/**
	 * @return PhanBo
	 */
	public function getHuynhTruong(): PhanBo {
		return $this->huynhTruong;
	}
	
	/**
	 * @param PhanBo $huynhTruong
	 */
	public function setHuynhTruong(PhanBo $huynhTruong): void {
		$this->huynhTruong = $huynhTruong;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getTargetDate(): \DateTime {
		return $this->targetDate;
	}
	
	/**
	 * @param \DateTime $targetDate
	 */
	public function setTargetDate(\DateTime $targetDate): void {
		$this->targetDate = $targetDate;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getCreatedAt(): \DateTime {
		return $this->createdAt;
	}
	
	/**
	 * @param \DateTime $createdAt
	 */
	public function setCreatedAt(\DateTime $createdAt): void {
		$this->createdAt = $createdAt;
	}
	
	/**
	 * @return float
	 */
	public function getPoint(): float {
		return $this->point;
	}
	
	/**
	 * @param float $point
	 */
	public function setPoint(float $point): void {
		$this->point = $point;
	}
	
	/**
	 * @return string
	 */
	public function getGhiNhan(): string {
		return $this->ghiNhan;
	}
	
	/**
	 * @param string $ghiNhan
	 */
	public function setGhiNhan(string $ghiNhan): void {
		$this->ghiNhan = $ghiNhan;
	}
	
	/**
	 * @return string
	 */
	public function getType(): string {
		return $this->type;
	}
	
	/**
	 * @param string $type
	 */
	public function setType(string $type): void {
		$this->type = $type;
	}
}
