<?php
namespace App\Doctrine\ORM\Listener\HoSo;

use App\Doctrine\ORM\Listener\DoctrineEntityListenerInterface;
use App\Entity\HoSo\PhanBo;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PhanBoListener implements DoctrineEntityListenerInterface {
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	function __construct(ContainerInterface $container) {
		$this->container = $container;
	}
	
	private function updateProperties(PhanBo $object) {
		
	}
	
	public function prePersistHandler(PhanBo $object, LifecycleEventArgs $event) {
		$this->updateProperties($object);
	}
	
	
	public function preUpdateHandler(PhanBo $object, LifecycleEventArgs $event) {
		$this->updateProperties($object);
	}
	
	public function postUpdateHandler(PhanBo $object, LifecycleEventArgs $event) {
		
	}
	
	public function preRemoveHandler(PhanBo $object, LifecycleEventArgs $event) {
		
	}
	
	public function postRemoveHandler(PhanBo $object, LifecycleEventArgs $event) {
	
	}
	
	public function postPersistHandler(PhanBo $employer, LifecycleEventArgs $event) {
	
	
	}
}