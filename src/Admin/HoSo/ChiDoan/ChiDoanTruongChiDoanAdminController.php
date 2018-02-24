<?php

namespace App\Admin\HoSo\ChiDoan;


use App\Admin\BaseAdmin;
use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class ChiDoanTruongChiDoanAdminController extends BaseCRUDAdminController {
	public function thieuNhiChiDoanChiaTruongPhuTrachAction($id = null, Request $request) {
		/**
		 * @var ChiDoan $chiDoan
		 */
		$chiDoan = $this->admin->getSubject();
		if( ! $chiDoan) {
			throw new NotFoundHttpException(sprintf('unable to find the Principal with id : %s', $id));
		}
		
		/** @var ChiDoanTruongChiDoanAdmin $admin */
		$admin = $this->admin;
		
		if( ! $admin->isGranted('chia-truong-phu-trach', $chiDoan)) {
			throw new AccessDeniedHttpException();
		}
		
		$admin->setAction('chia-truong-phu-trach');
		$admin->setActionParams([ 'chiDoan' => $chiDoan, 'christianNames' => ThanhVien::$christianNames ]);
		if($request->isMethod('post')) {
			$doi1     = $request->request->getInt('doi1', 0);
			$doi2     = $request->request->getInt('doi2', 0);
			$doi3     = $request->request->getInt('doi3', 0);
			$doi4     = $request->request->getInt('doi4', 0);
			$doi5     = $request->request->getInt('doi5', 0);
			$doi6     = $request->request->getInt('doi6', 0);
			$doi7     = $request->request->getInt('doi7', 0);
			$doi8     = $request->request->getInt('doi8', 0);
			
			$phanBoId = $request->request->get('phanBoId');
			if( ! (empty($phanBo = $this->getDoctrine()->getRepository(PhanBo::class)->find($phanBoId)))) {
				if($chiDoan->chiaTruongPhuTrachVaoCacDoi($doi1, $doi2, $doi3,$doi4,$doi5,$doi6,$doi7,$doi8, $phanBo)) {
					$manager = $this->get('doctrine.orm.default_entity_manager');
					$manager->persist($phanBo);
					$manager->persist($chiDoan);
					$manager->flush();
				};
				
				return new JsonResponse('OK', 200);
			} else {
				return new JsonResponse([ 415, 'Unsupported Data Type' ], 415);
			}
		}
		
		return parent::listAction();
	}
	
	public function thieuNhiChiDoanChiaDoiAction($id = null, Request $request) {
		
		/**
		 * @var ChiDoan $chiDoan
		 */
		$chiDoan = $this->admin->getSubject();
		if( ! $chiDoan) {
			throw new NotFoundHttpException(sprintf('unable to find the Principal with id : %s', $id));
		}
		
		/** @var ChiDoanTruongChiDoanAdmin $admin */
		$admin = $this->admin;
		
		if( ! $admin->isGranted('chia-doi-thieu-nhi', $chiDoan)) {
			throw new AccessDeniedHttpException();
		}
		
		$admin->setAction('chia-doi-thieu-nhi');
		$admin->setActionParams([ 'chiDoan' => $chiDoan, 'christianNames' => ThanhVien::$christianNames ]);
		if($request->isMethod('post')) {
			$doi      = $request->request->getInt('doi', 0);
			$phanBoId = $request->request->get('phanBoId');
			if( ! (empty($doi) || empty($phanBo = $this->getDoctrine()->getRepository(PhanBo::class)->find($phanBoId)))) {
				$dngl = $chiDoan->getDoiNhomGiaoLy($doi);
				$dngl->getPhanBoThieuNhi()->add($phanBo);
				$phanBo->setDoiNhomGiaoLy($dngl);
				$manager = $this->get('doctrine.orm.default_entity_manager');
				$manager->persist($dngl);
				$manager->persist($phanBo);
				$manager->persist($chiDoan);
				$manager->flush();
				
				return new JsonResponse('OK', 200);
			} else {
				return new JsonResponse([ 415, 'Unsupported Data Type' ], 415);
			}
		}
		
		return parent::listAction();
	}
	
	
}