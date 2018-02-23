<?php

namespace App\Admin\HocBa\BangDiem;

use App\Admin\BaseAdmin;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class BangDiemAdmin extends BaseAdmin {
	
	public const AUTO_CONFIG = false;
	
	const ENTITY = BangDiem::class;
	
	/**
	 * @var integer
	 */
	protected $namHoc;
	
	protected $action = '';
	protected $actionParams = [];
	
	protected $datagridValues = array(
		// display the first page (default = 1)
		'_page'       => 1,
		
		// reverse order (default = 'DESC')
		'_sort_order' => 'ASC',
		
		// name of the ordered field (default = the model's id field, if any)
		'_sort_by'    => 'phanBo.thanhVien.firstname',
	);
	
	
	public function getTemplate($name) {
		if($name === 'list') {
			return 'admin/bang-diem/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
//		$collection->add('bangDiemImport', 'import/{namHoc}');
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$danhSachChiDoan = [
			'Chiên Con 4 tuổi'   => 4,
			'Chiên Con 5 tuổi'   => 5,
			'Chiên Con 6 tuổi'   => 6,
			'Ấu Nhi 7 tuổi'      => 7,
			'Ấu Nhi 8 tuổi'      => 8,
			'Ấu Nhi 9 tuổi'      => 9,
			'Thiếu Nhi 10 tuổi'  => 10,
			'Thiếu Nhi 11 tuổi'  => 11,
			'Thiếu Nhi 12 tuổi'  => 12,
			'Nghĩa Sĩ 13 tuổi'   => 13,
			'Nghĩa Sĩ 14 tuổi'   => 14,
			'Nghĩa Sĩ 15 tuổi'   => 15,
			'Tông Đồ 16 tuổi'    => 16,
			'Tông Đồ 17 tuổi'    => 17,
			'Tông Đồ 18 tuổi'    => 18,
			'Dự Trưởng(19 tuổi)' => 19,
		];
		
		// this text filter will be used to retrieve autocomplete fields
//		$datagridMapper
//			->add('phanBo.phanDoan', 'doctrine_orm_choice', array(
//				'show_filter' => true,
//			), 'choice', array(
//				'choices' => ThanhVien::$danhSachPhanDoan
//			))
//			->add('phanBo.chiDoan.name', 'doctrine_orm_choice', array(
//				'show_filter' => true,
//			), 'choice', array(
//				'choices' => $danhSachChiDoan
//			))
//			->add('phanBo.chiDoan.namHoc.id', null, array(
//				'show_filter' => true,
//			));
	}
	
	public function isGranted($name, $object = null) {

		
		$tv = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if(in_array($name, [ 'LIST', 'SHOW' ])) {
			return true;
		}
		
		if(empty($this->namHoc)) {
			
			return false;
		}
		
		if($name === 'IMPORT') {

//			return true;
		} elseif($name === 'CREATE') {
		
		}
		
		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query = parent::createQuery($context);
		
		return $query;
	}
	
	public function generateUrl($name, array $parameters = array(), $absolute = UrlGeneratorInterface::ABSOLUTE_PATH) {
		if($name === 'list') {
			$request = $this->getRequest();
			$id      = $request->query->get('id');
			$hocKy   = $request->query->get('hocKy');
			
			$parameters['id']    = $id;
			$parameters['hocKy'] = $hocKy;
			
		} elseif($name === 'edit') {
		
		}
		
		return parent::generateUrl($name, $parameters, $absolute);
	}
	
	public function getPersistentParameters() {
		$parameters = parent::getPersistentParameters();
		if( ! $this->hasRequest() || empty($this->action)) {
			return $parameters;
		}
		$request = $this->getRequest();
		$id      = $request->query->get('id');
		$hocKy   = $request->query->get('hocKy');
		
		return array_merge($parameters, array(
			'action' => $this->action,
			'id'     => $id,
			'hocKy'  => $hocKy
		));
	}
	
	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
//			->addIdentifier('id')
//			->addIdentifier('id', null, array())
//			->addIdentifier('phanBo.thanhVien', null, array(
//				'label'               => 'list.label_full_name',
//				'associated_property' => 'name',
//				'admin_code'         => 'app.admin.binhle_thieunhi_thieunhi'
//			));
			->add('phanBo.thanhVien.firstname', null, array(
				'label'    => 'list.label_full_name'
			,
				'template' => '::admin/binhle/thieu-nhi/chi-doan/bang-diem/list__field__name.html.twig'
			));
		$request = $this->getRequest();
		if($request->query->getInt('hocKy') === 1) {
			$listMapper
				->add('cc9')
				->add('cc10')
				->add('cc11')
				->add('cc12')
				->add('tbCCTerm1', null, array( 'label' => 'list.label_tb_cc_term1' ))
				->add('quizTerm1')
				->add('midTerm1')
				->add('finalTerm1')
				->add('sundayTicketTerm1');
			
		} else {
			$listMapper
				->add('cc1')
				->add('cc2')
				->add('cc3')
				->add('cc4')
				->add('cc5')
				->add('tbCCTerm2', null, array( 'label' => 'list.label_tb_cc_term2' ))
				->add('quizTerm2')
				->add('midTerm2')
				->add('finalTerm2')
				->add('sundayTicketTerm2');
		}
//		$listMapper->add('phanBo.chiDoan.id', null, array(
//			'label' => 'list.label_chi_doan'
//		));
	}
	
	protected function configureFormFields(FormMapper $formMapper) {
		$isAdmin   = $this->isAdmin();
		$container = $this->getConfigurationPool()->getContainer();
		
		$danhSachChiDoan = [
			'Chiên Con 4 tuổi'   => 4,
			'Chiên Con 5 tuổi'   => 5,
			'Chiên Con 6 tuổi'   => 6,
			'Ấu Nhi 7 tuổi'      => 7,
			'Ấu Nhi 8 tuổi'      => 8,
			'Ấu Nhi 9 tuổi'      => 9,
			'Thiếu Nhi 10 tuổi'  => 10,
			'Thiếu Nhi 11 tuổi'  => 11,
			'Thiếu Nhi 12 tuổi'  => 12,
			'Nghĩa Sĩ 13 tuổi'   => 13,
			'Nghĩa Sĩ 14 tuổi'   => 14,
			'Nghĩa Sĩ 15 tuổi'   => 15,
			'Tông Đồ 16 tuổi'    => 16,
			'Tông Đồ 17 tuổi'    => 17,
			'Tông Đồ 18 tuổi'    => 18,
			'Dự Trưởng(19 tuổi)' => 19,
		];
		
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general')//            ->add('children')
		;
		$formMapper
			->add('namHoc')
			->add('chiDoan', ChoiceType::class, array(
				'placeholder'        => 'Chọn Chi Đoàn',
				'choices'            => $danhSachChiDoan,
				'translation_domain' => $this->translationDomain
			));
		
		$formMapper
			->end()
			->end();
		
	}
	
	/**
	 * @param BangDiem $object
	 */
	public function preValidate($object) {
	}
	
	/**
	 * @return integer
	 */
	public function getNamHoc() {
		return $this->namHoc;
	}
	
	/**
	 * @param integer $namHoc
	 */
	public function setNamHoc($namHoc) {
		$this->namHoc = $namHoc;
	}
	
	/**
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}
	
	/**
	 * @param string $action
	 */
	public function setAction($action) {
		$this->action = $action;
	}
	
	/**
	 * @return array
	 */
	public function getActionParams() {
		return $this->actionParams;
	}
	
	/**
	 * @param array $actionParams
	 */
	public function setActionParams($actionParams) {
		$this->actionParams = $actionParams;
	}
	
}