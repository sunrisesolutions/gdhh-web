<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity\User;

use App\Entity\User\Base\AppUser;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user__user")
 *
 */
class User extends AppUser {
	public function isAdmin() {
		return $this->hasRole(self::ROLE_ADMIN) || $this->hasRole(self::ROLE_SUPER_ADMIN);
	}
	
	public static function generate4DigitCode($code = null) {
		if($code === null) {
			$code = rand(0, 1679615);
		}
		
		$code = base_convert($code, 10, 36);
		for($i = 0; $i < 4 - strlen($code);) {
			$code = '0' . $code;
		}
		
		return $code;
	}
}
