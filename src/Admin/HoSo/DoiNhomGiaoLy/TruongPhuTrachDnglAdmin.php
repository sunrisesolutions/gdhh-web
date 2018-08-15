<?php

namespace App\Admin\HoSo\DoiNhomGiaoLy;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Service\HoSo\NamHocService;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class TruongPhuTrachDnglAdmin extends BaseAdmin {
	
	const ENTITY = DoiNhomGiaoLy::class;
	
	protected $baseRouteName = 'admin_app_hoso_doinhomgiaoly_truongphutrach_dngl';
	
	protected $baseRoutePattern = '/app/hoso-doinhomgiaoly/truongphutrach-doi';
	
	public function getTemplate($name) {
		if($name === 'list') {
			return 'admin/doi-nhom-giao-ly/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
		parent::configureRoutes($collection);
		$collection->add('baoCaoTienQuy', 'bao-cao-tien-quy');
		$collection->add('bangDiem', $this->getRouterIdParameter() . '/bang-diem/{hocKy}/{action}');
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id', null, [ 'label' => 'list.label_id' ]);
	}
	
	/**
	 * @param string        $name
	 * @param DoiNhomGiaoLy $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		if(is_array($name)) {
			$name = $name[0];
		}
		
		if($name === 'EDIT') {
			return false;
		}
		
		$container = $this->getConfigurationPool()->getContainer();
		
		if($this->isAdmin()) {
			return true;
		}
		
		$tv = $thanhVien = $this->getUserThanhVien();
		
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if($thanhVien->isChiDoanTruong()) {
			$phanBoNamNay = $thanhVien->getPhanBoNamNay();
			if($phanBoNamNay->isChiDoanTruong()) {
				if(in_array($name, [ 'LIST', 'duyet-bang-diem' ])) {
					return true;
				}
				if( ! empty($object)) {
					if($phanBoNamNay->getChiDoan()->getId() === $object->getChiDoan()->getId()) {
						return true;
					}
				}
				
				return false;
			}
		} else {
			if($name !== 'LIST') {
				return false;
			}
			if($thanhVien->isBQT()) {
				return true;
			}
		}
		
		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query = parent::createQuery($context);
		/** @var Expr $expr */
		$expr = $query->expr();
		/** @var QueryBuilder $qb */
		$qb        = $query->getQueryBuilder();
		$rootAlias = $qb->getRootAliases()[0];
		if( ! empty($chiDoan = $this->getUserChiDoan())) {
			$query->andWhere($expr->eq($rootAlias . '.chiDoan', $chiDoan));
			$qb->join($rootAlias . '.chiDoan', 'chiDoan')
			   ->join('chiDoan.namHoc', 'namHoc');
			$query->andWhere($expr->eq('namHoc.id', $this->getConfigurationPool()->getContainer()->get(NamHocService::class)->getNamHocHienTai()->getId()));
		}
		
		return $query;
	}
	
	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
//			->addIdentifier('id')
			->add('id', 'text', array());
		if($this->action === 'bao-cao-tien-quy') {
			$listMapper->add('_progress', null, array(
				'header_style' => 'width: 30%; text-align: center',
				
				'label'    => 'list.label_progress'
			,
				'template' => 'admin/doi-nhom-giao-ly/list-bao-cao-tien-quy__field__progress.html.twig'
			));
			$listMapper->add('_so_thieu_nhi', null, array(
				'label'    => 'list.label_so_thieu_nhi'
			,
				'template' => 'admin/doi-nhom-giao-ly/list-bao-cao-tien-quy__field__so_thieu_nhi.html.twig'
			));
			$listMapper->add('_so_tien', null, array(
				'label'    => 'list.label_so_tien'
			,
				'template' => 'admin/doi-nhom-giao-ly/list-bao-cao-tien-quy__field__so_tien.html.twig'
			));
			$listMapper->add('_so_thieu_nhi_ngheo', null, array(
				'label'    => 'list.label_so_thieu_nhi_ngheo'
			,
				'template' => 'admin/doi-nhom-giao-ly/list-bao-cao-tien-quy__field__so_thieu_nhi_ngheo.html.twig'
			));
		} else {
			$listMapper
				->add('number', null, array(
					'label'    => 'list.label_nhom_giao_ly',
					'template' => 'admin/doi-nhom-giao-ly/list__field__doi_giao_ly.html.twig'
				))
				->add('_action', 'actions', array(
					'actions' => array(
						'duyet_bang_diem' => array( 'template' => 'admin/doi-nhom-giao-ly/list__action__duyet_bang_diem.html.twig' ),
						'delete'          => array(),
//                ,
//                    'view_description' => array('template' => '::admin/product/description.html.twig')
//                ,
//                    'view_tos' => array('template' => '::admin/product/tos.html.twig')
					)
				));
			
		}
	}
	
	protected function configureFormFields(FormMapper $formMapper) {
		$isAdmin   = $this->isAdmin();
		$container = $this->getConfigurationPool()->getContainer();
		
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general')//            ->add('children')
			->add('id', null, array( 'label' => 'list.label_nam_hoc' ));
		
		
		$formMapper
			->end()
			->end();
		
	}
	
	/**
	 * @param DoiNhomGiaoLy $object
	 */
	public function preValidate($object) {
	
	}
	
	/** @param DoiNhomGiaoLy $object */
	public function prePersist($object) {
	
	}
	
	public function getUserThanhVien() {
		return parent::getUserThanhVien();
	}
}
