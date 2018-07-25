<?php

namespace App\Entity\HoSo;

use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__christian_name")
 */
class ChristianName {
	
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
		$this->cacThanhVien = new ArrayCollection();
	}
	
	/**
	 * @var ArrayCollection
	 * @ORM\OneToMany(targetEntity="App\Entity\HoSo\ThanhVien", mappedBy="tenThanh")
	 */
	protected $cacThanhVien;
	
	/**
	 * @var integer
	 * @ORM\Column(type="integer", nullable=true)
	 */
	protected $position;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=6, nullable=true)
	 */
	protected $sex;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $tiengViet;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=250, nullable=true)
	 */
	protected $tiengAnh;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", length=250, nullable=true, unique=true)
	 */
	protected $code;
	
	
	/**
	 * @return string
	 */
	public function getTiengViet() {
		return $this->tiengViet;
	}
	
	/**
	 * @param string $tiengViet
	 */
	public function setTiengViet($tiengViet) {
		$this->tiengViet = $tiengViet;
	}
	
	/**
	 * @return string
	 */
	public function getTiengAnh() {
		return $this->tiengAnh;
	}
	
	/**
	 * @param string $tiengAnh
	 */
	public function setTiengAnh($tiengAnh) {
		$this->tiengAnh = $tiengAnh;
	}
	
	/**
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}
	
	/**
	 * @param string $code
	 */
	public function setCode($code) {
		$this->code = $code;
	}
	
	/**
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}
	
	/**
	 * @param int $position
	 */
	public function setPosition($position) {
		$this->position = $position;
	}
	
	/**
	 * @return ArrayCollection
	 */
	public function getCacThanhVien() {
		return $this->cacThanhVien;
	}
	
	/**
	 * @param ArrayCollection $cacThanhVien
	 */
	public function setCacThanhVien($cacThanhVien) {
		$this->cacThanhVien = $cacThanhVien;
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
}