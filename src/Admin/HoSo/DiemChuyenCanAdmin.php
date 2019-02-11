<?php

namespace App\Admin\HoSo;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\DiemChuyenCan;
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

class DiemChuyenCanAdmin extends BaseAdmin {
	
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
			->add('id', null, [ 'label' => 'list.label_id' ])
            ->add('targetDate','doctrine_orm_date')
            
        ;
	}
	
	/**
	 * @param string $name
	 * @param DiemChuyenCan $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		$container = $this->getConfigurationPool()->getContainer();
		
		
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
            ->add('pointValue','text',['editable'=>true])
            ->add('massCounted',null,['editable'=>true])
            ->add('studyCounted',null,['editable'=>true])
            ->add('targetDate')

;
	}
	
	/**
	 * @param DiemChuyenCan $object
	 */
	public function preValidate($object) {
	
	}
	
	/** @param DiemChuyenCan $object */
	public function prePersist($object) {
		$object->setEnabled(false);
	}
}