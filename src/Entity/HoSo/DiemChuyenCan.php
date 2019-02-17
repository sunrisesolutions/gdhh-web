<?php

namespace App\Entity\HoSo;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__diem_chuyen_can")
 */
class DiemChuyenCan {
	
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer",options={"unsigned":true})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * @return mixed
	 */
	public function getId() {
		return $this->id;
	}
    
    public function isStudyCounted():bool {
        return !empty($this->studyCounted);
    }
    
    public function isMassCounted():bool {
        return !empty($this->massCounted);
    }
    
    /**
     * @ORM\Column(type="boolean",nullable=true,options={"default": true})
     * @var int|null
     */
    protected $massCounted = true;
    
    /**
     * @ORM\Column(type="boolean",nullable=true,options={"default": true})
     * @var int|null
     */
    protected $studyCounted = true;
    
    
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
    
    /**
     * @return int|null
     */
    public function getMassCounted(): ?int
    {
        return $this->massCounted;
    }
    
    /**
     * @param int|null $massCounted
     */
    public function setMassCounted(?int $massCounted): void
    {
        $this->massCounted = $massCounted;
    }
    
    /**
     * @return int|null
     */
    public function getStudyCounted(): ?int
    {
        return $this->studyCounted;
    }
    
    /**
     * @param int|null $studyCounted
     */
    public function setStudyCounted(?int $studyCounted): void
    {
        $this->studyCounted = $studyCounted;
    }
	
}
