<?php

namespace App\Entity\User\Base;

use App\Entity\HoSo\ThanhVien;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
class AppUser {
	
	const ROLE_ADMIN = 'ROLE_ADMIN';
	const ROLE_HUYNH_TRUONG = 'ROLE_HUYNH_TRUONG';
	
	/**
	 * @var int
	 * @ORM\Id
	 * @ORM\Column(type="integer",options={"unsigned":true})
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	function __construct() {
		parent::__construct();
	}
	
	/**
	 * @var ThanhVien
	 * @ORM\OneToOne(targetEntity="App\Entity\HoSo\ThanhVien", mappedBy="user", cascade={"persist","merge"})
	 */
	protected $thanhVien;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $middlename;
	
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $christianName;
	
	/**
	 * @var  ArrayCollection $addresses
	 */
	protected $addresses;
	
	/**
	 * @var Media
	 */
	protected $avatar;
	
	/**
	 * @var string
	 */
	protected $maritalStatus;
	
	/**
	 * @var string
	 */
	protected $nationality;
	
	/**
	 * @return mixed
	 */
	public function getIndividualEntity() {
		return $this->individualEntity;
	}
	
	/**
	 * @param mixed $individualEntity
	 */
	public function setIndividualEntity($individualEntity) {
		$this->individualEntity = $individualEntity;
	}
	
	/**
	 * @return ThanhVien
	 */
	public function getThanhVien() {
		return $this->thanhVien;
	}
	
	/**
	 * @param ThanhVien $thanhVien
	 */
	public function setThanhVien($thanhVien) {
		$this->thanhVien = $thanhVien;
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
	
}