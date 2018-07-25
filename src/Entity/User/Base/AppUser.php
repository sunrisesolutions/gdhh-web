<?php
namespace App\Entity\User\Base;

use App\Entity\HoSo\ThanhVien;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use FOS\UserBundle\Model\User as AbstractUser;

/** @ORM\MappedSuperclass */
class AppUser extends AbstractUser {
	
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
	
	
}