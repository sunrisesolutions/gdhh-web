<?php

namespace App\Entity\Media\Base;

use App\Entity\Media\Media;
use App\Entity\User\User;
use Sonata\MediaBundle\Entity\BaseMedia;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\MappedSuperclass */
class AppMedia extends BaseMedia {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", length=24)
	 * @ORM\GeneratedValue(strategy="CUSTOM")
	 * @ORM\CustomIdGenerator(class="App\Doctrine\ORM\RandomIdGenerator")
	 */
	protected $id;
	
	function __construct() {
		$this->enabled             = true;
	}
	
	/**
	 * @var Media
	 */
	protected $thumbnail;
	
	/**
	 * @var User
	 */
	protected $avatarUser;
	
	
	/**
	 * Get id
	 *
	 * @return int $id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * @return Media
	 */
	public function getThumbnail(): Media {
		return $this->thumbnail;
	}
	
	/**
	 * @param Media $thumbnail
	 */
	public function setThumbnail(Media $thumbnail): Media {
		$this->thumbnail = $thumbnail;
	}
	
	/**
	 * @return User
	 */
	public function getAvatarUser(): User {
		return $this->avatarUser;
	}
	
	/**
	 * @param User $avatarUser
	 */
	public function setAvatarUser(User $avatarUser): void {
		$this->avatarUser = $avatarUser;
	}
}