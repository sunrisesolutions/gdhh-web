<?php

namespace App\Entity\HoSo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__diem_chuyen_can")
 */
class DiemChuyenCan {
	
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=24)
	 */
	protected $id;
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @var \DateTime
	 * @ORM\Column(type="date", nullable=true)
	 */
	protected $targetDate;
	
	/**
	 * @ORM\Column(type="integer",nullable=true,options={"default":0})
	 * @var int|null
	 */
	protected $pointValue = 0;
	
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
	 * @return int|null
	 */
	public function getPointValue(): ?int {
		return $this->pointValue;
	}
	
	/**
	 * @param int|null $pointValue
	 */
	public function setPointValue(?int $pointValue): void {
		$this->pointValue = $pointValue;
	}
	
	
}
