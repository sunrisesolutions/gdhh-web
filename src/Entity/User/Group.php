<?php

namespace App\Entity\User;

use App\Entity\User\Base\AppGroup as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity
 * @ORM\Table(name="user__group")
 *
 */
class Group extends BaseGroup
{

}
