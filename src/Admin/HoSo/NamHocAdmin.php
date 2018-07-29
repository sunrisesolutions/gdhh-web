<?php

namespace App\Admin\HoSo;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\ThanhVien;
use App\Service\User\UserService;
use Bean\Bundle\CoreBundle\Service\StringService;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Form\Type\ModelType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\CollectionType;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Datagrid;
use Sonata\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Valid;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class NamHocAdmin extends BaseAdmin {
	
	public function getTemplate($name) {
		if($name === 'list') {
//			return '::admin/binhle/thieu-nhi/phan-bo/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	
	public function configureRoutes(RouteCollection $collection) {
		$collection->add('khaiGiang', $this->getRouterIdParameter() . '/khai-giang');
		$collection->add('khoaSo', $this->getRouterIdParameter() . '/khoa-so');
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id', null, [ 'label' => 'list.label_id' ]);
	}
	
	/**
	 * @param string $name
	 * @param NamHoc $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		$container = $this->getConfigurationPool()->getContainer();
		
		
		if($name === 'DELETE') {
			return false;
		}
		
		if(empty($this->namHoc)) {
		}
		
		$tv = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if($name === 'KHAI_GIANG') {
			if(($object->isStarted())) {
				return false;
			}
			/** @var QueryBuilder $qb */
			$qb    = $container->get('doctrine')->getRepository(NamHoc::class)->createQueryBuilder('o');
			$expr  = $qb->expr();
			$query = $qb
				->where($expr->eq('o.enabled', ':trueValue'))
				->setParameter('trueValue', true)
				->getQuery();

//			$x = $query->getSQL();
			return empty($query->getOneOrNullResult());
//			return true;
		} elseif($name === 'KHOA_SO') {
			if($object->isStarted() && $object->isEnabled()) {
				return true;
			}
			
			return false;
		}
		
		if($this->isAdmin()) {
			return true;
		}
		$user = $container->get(UserService::class)->getUser();
		if(empty($thanhVien = $user->getThanhVien())) {
			return false;
		} elseif($thanhVien->isBQT()) {
			return true;
		}
		
		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query = parent::createQuery($context);
		
		return $query;
	}
	
	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
//			->addIdentifier('id')
			->add('id', 'text', array())
			->add('started', null, array(//				'template' => '::admin/binhle/thieu-nhi/nam-hoc/list__field_started.html.twig'
				'editable' => false,
			))
			->add('enabled', null, array(
				'label'    => 'list.label_khoa_so',
				'editable' => false,
//				'template' => '::admin/binhle/thieu-nhi/nam-hoc/list__field_enabled.html.twig'
			))
			->add('diemTB', null, array(
				'label' => 'list.label_diem_tb'
			))
			->add('diemKha')
			->add('diemGioi')
			->add('phieuLenLop')
			->add('phieuKhenThuong')
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
//					'send_evoucher' => array( 'template' => '::admin/employer/employee/list__action_send_evoucher.html.twig' )

//                ,
//                    'view_description' => array('template' => '::admin/product/description.html.twig')
//                ,
//                    'view_tos' => array('template' => '::admin/product/tos.html.twig')
				)
			));
	}
	
	protected function configureFormFields(FormMapper $formMapper) {
		$isAdmin   = $this->isAdmin();
		$container = $this->getConfigurationPool()->getContainer();
		
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general')//            ->add('children')
			->add('id', null, array( 'label' => 'list.label_nam_hoc' ))
			->add('namTruoc', ModelType::class, array( 'property' => 'id' ))
			->add('diemTB', null, array(
				'label' => 'list.label_diem_tb'
			))
			->add('diemKha', null, array(
				'label' => 'list.label_diem_kha'
			))
			->add('diemGioi', null, array(
				'label' => 'list.label_diem_gioi'
			))
			->add('phieuLenLop', null, array(
				'label' => 'list.label_phieu_len_lop'
			))
			->add('phieuKhenThuong', null, array(
				'label' => 'list.label_phieu_khen_thuong'
			));
		
		
		$formMapper
			->end()
			->end();
		
	}
	
	/**
	 * @param NamHoc $object
	 */
	public function preValidate($object) {
	
	}
	
	/** @param NamHoc $object */
	public function prePersist($object) {
		$object->setEnabled(false);
	}
}