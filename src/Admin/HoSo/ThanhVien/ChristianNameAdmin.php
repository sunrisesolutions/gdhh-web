<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\ChristianName;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class ChristianNameAdmin extends BaseAdmin {
	const ENTITY = ChristianName::class;
	
	/**
	 * @var integer
	 */
	protected $namHoc;
	
	protected $action = '';
	protected $actionParams = [];
	
	
	public function configureRoutes(RouteCollection $collection) {
//		$collection->add('bangDiemImport', 'import/{namHoc}');
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		
	}
	
	public function isGranted($name, $object = null) {
		
		$tv = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
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
//			->addIdentifier('id', null, array())
			->addIdentifier('id')
			->add('tiengViet', null, array( 'editable' => true ))
			->add('tiengAnh', null, array( 'editable' => true ))
			->add('sex', null, array( 'editable' => true ))
			->add('code', null, array( 'editable' => true ));
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
			->add('tiengViet')
			->add('tiengAnh')
			->add('code');
		
		$formMapper
			->end()
			->end();
		
	}
	
	/**
	 * @param ChristianName $object
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