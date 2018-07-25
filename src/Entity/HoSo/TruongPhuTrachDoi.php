<?php

namespace App\Entity\HoSo;

use App\Entity\Content\Base\AppContentEntity;
use App\Entity\NLP\Sense;
use App\Entity\User\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="hoso__truong_phu_trach_doi")
 */
class TruongPhuTrachDoi {
	
	/**
	 * @var string
	 * @ORM\Id
	 * @ORM\Column(type="string")
	 */
	protected $id;
	
	
	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}
	
	function __construct() {
	
	}
	
	public function generateId() {
		$this->id = User::generate4DigitCode() . '-' . $this->phanBo->getThanhVien()->getCode() . '-' . $this->doiNhomGiaoLy->getId();
	}
	
	/**
	 * @var DoiNhomGiaoLy
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\DoiNhomGiaoLy", inversedBy="cacTruongPhuTrachDoi", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_doi_nhom_giao_ly", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $doiNhomGiaoLy;
	
	/**
	 * @var PhanBo
	 * @ORM\ManyToOne(targetEntity="App\Entity\HoSo\PhanBo", inversedBy="cacTruongPhuTrachDoi", cascade={"persist","merge"})
	 * @ORM\JoinColumn(name="id_phan_bo_hang_nam", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $phanBo;
	
	/**
	 * @return DoiNhomGiaoLy
	 */
	public function getDoiNhomGiaoLy() {
		return $this->doiNhomGiaoLy;
	}
	
	/**
	 * @param DoiNhomGiaoLy $doiNhomGiaoLy
	 */
	public function setDoiNhomGiaoLy($doiNhomGiaoLy) {
		$this->doiNhomGiaoLy = $doiNhomGiaoLy;
	}
	
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
	
	
}