<?php

namespace App\Admin\HoSo;

use App\Admin\BaseCRUDAdminController;
use App\Admin\HoSo\NamHocAdmin;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\ThanhVien;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NamHocAdminController extends BaseCRUDAdminController {
	
	public function khaiGiangAction($id, Request $request) {
		/** @var NamHocAdmin $admin */
		$admin = $this->admin;
		/** @var NamHoc $object */
		$object = $admin->getSubject();
		if( ! $object) {
			throw new NotFoundHttpException(sprintf('unable to find the School Year with id : %s', $id));
		}
		$manager = $this->get('doctrine.orm.entity_manager');
		if( ! $admin->isGranted('KHAI_GIANG', $object)) {
			throw new AccessDeniedHttpException();
		}
		/** Roll out ThanhVien first */
		/** @var QueryBuilder $qb */
		$qb    = $this->get('doctrine')->getRepository(ThanhVien::class)->createQueryBuilder('o');
		$expr  = $qb->expr();
		$query = $qb
			->where($expr->eq('o.enabled', ':trueValue'))
			->andWhere($expr->eq('o.thieuNhi', ':trueValue'))
			->setParameter('trueValue', true)
			->getQuery();
		
		$results = $query->getResult();
		/** @var ThanhVien $tv */
		foreach($results as $tv) {
			if( ! empty($phanBoMoi = $tv->chuyenNhom($object))) {
				$manager->persist($phanBoMoi);
			}
		}
		$object->setStarted(true);
		$object->setEnabled(true);
		$manager->flush();

//		$admin->setAction('list-thieu-nhi');
		return parent::listAction();
	}
	
	public function khoaSoAction($id, Request $request) {
		
		/** @var NamHocAdmin $admin */
		$admin = $this->admin;
		/** @var NamHoc $object */
		$object = $admin->getSubject();
		if( ! $object) {
			throw new NotFoundHttpException(sprintf('unable to find the School Year with id : %s', $id));
		}
		if( ! $admin->isGranted('KHOA_SO', $object)) {
			throw new AccessDeniedHttpException();
		}
		$object->setEnabled(false);
		$manager = $this->get('doctrine.orm.entity_manager');
		$manager->persist($object);
		$manager->flush();

//		$admin->setAction('list-thieu-nhi');
		return parent::listAction();
	}
	
}