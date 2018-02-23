<?php

namespace App\Admin\HocBa\BangDiem;

use App\Entity\HocBa\BangDiem;
use App\Entity\HoSo\ThanhVien;
use Bean\Bundle\CoreBundle\Service\StringService;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid;
use Sonata\DoctrineORMAdminBundle\Filter\Filter;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Valid;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class PhanDoanTruongBangDiemAdmin extends BangDiemAdmin {
	
	public const AUTO_CONFIG = true;
	
	protected $baseRouteName = 'admin_app_hocba_phandoantruong_bangdiem';
	protected $baseRoutePattern = '/app/hocba-bangdiem/phandoantruong-xem-bangdiem';
	
	protected $action = '';
	protected $actionParams = [];
	
	public function getTemplate($name) {
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
//		$collection->add('bangDiemImport', 'import/{namHoc}');
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		parent::configureDatagridFilters($datagridMapper);
	}
	
	public function isGranted($name, $object = null) {
		
		$tv = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if( ! $tv->isBQT() && ! $tv->isPhanDoanTruongOrSoeur()) {
			return false;
		}
		
		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query    = parent::createQuery($context);
		$tv       = $this->getUserThanhVien();
		$phanDoan = $tv->getPhanDoan();
		$year     = $tv->getNamHoc();
		
		/** @var QueryBuilder $qb */
		$qb        = $query->getQueryBuilder();
		$expr      = $qb->expr();
		$rootAlias = $qb->getRootAliases()[0];
		
		$qb->join($rootAlias . '.phanBo', 'phanBo');
		$qb->join('phanBo.chiDoan', 'chiDoan');
		$qb->join('phanBo.doiNhomGiaoLy', 'doiNhomGiaoLy');
		$qb->join('chiDoan.namHoc', 'namHoc');
		
		$chiDoan = $this->getRequest()->query->get('id');
		if( ! empty($chiDoan)) {
			$query->andWhere($expr->eq('chiDoan.id', $expr->literal($chiDoan)));
		} else {
			$query->andWhere($expr->eq('chiDoan.phanDoan', $expr->literal($phanDoan)));
		}
		$query->andWhere($expr->eq('namHoc.id', $year));
		$x = $qb->getQuery()->getSQL();
		
		return $query;
	}
	
	protected function configureListFields(ListMapper $listMapper) {
		parent::configureListFields($listMapper);
	}
	
	protected function configureFormFields(FormMapper $formMapper) {
		parent::configureFormFields($formMapper);
	}
	
	/**
	 * @param BangDiem $object
	 */
	public function preValidate($object) {
	}
	
	
}