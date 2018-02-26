<?php

namespace App\Admin\HoSo\PhanBo;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
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

class TruongPhuTrachDoiAdmin extends BaseAdmin {
	
	const ENTITY = PhanBo::class;
	
	protected $baseRouteName = 'admin_app_hoso_phanbo_truongphutrachdoi';
	
	protected $baseRoutePattern = '/app/hoso-phanbo/truongphutrachdoi';
	
	/** @var  NamHoc $namHoc */
	public $namHoc;
	
	public function getTemplate($name) {
		if($name === 'list') {
			if($this->action === 'dong-quy') {
				return 'admin/truong-phu-trach-doi/list-dong-quy-thieu-nhi.html.twig';
			}
			if($this->action === 'nhap-diem-thieu-nhi' || $this->action === 'nop-bang-diem') {
				return 'admin/truong-phu-trach-doi/list-nhap-diem-thieu-nhi.html.twig';
			}
		}
		
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
		parent::configureRoutes($collection);
		$collection->add('dongQuy', $this->getRouterIdParameter() . '/dong-quy');
		$collection->add('nhapDiemThieuNhi', $this->getRouterIdParameter() . '/nhap-diem-thieu-nhi');
		$collection->add('thieuNhiNhomDownloadBangDiem',  $this->getRouterIdParameter() . '/bang-diem/hoc-ky-{hocKy}/download');
		$collection->add('nopBangDiem', $this->getRouterIdParameter() . '/nop-bang-diem/{hocKy}');
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id');
	}
	
	/**
	 * @param string $name
	 * @param PhanBo $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		
		$tv = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return false;
		}
		
		if($name === 'NOP_BANG_DIEM') {
			if(empty($object) || empty($hocKy = $this->actionParams['hocKy'])) {
				return false;
			}
			
			return $object->coTheNopBangDiem($hocKy);
		}
		
		if($name === 'LIST') {
			return true;
		}
		
		return false;
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
			->add('_action', 'actions', array(
				'actions' => array(
					'edit'   => array(),
					'delete' => array(),
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
			->add('id', null, array( 'label' => 'list.label_nam_hoc' ));
		
		
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
	
	}
}