<?php

namespace App\Admin\HoSo\ChiDoan;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ThanhVien;
use App\Service\User\UserService;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class ChiDoanTruongChiDoanAdmin extends BaseAdmin {
	
	const ENTITY = ChiDoan::class;
	
	protected $baseRouteName = 'admin_app_hoso_chidoan_chidoantruong_chidoan';
	
	protected $baseRoutePattern = '/app/hoso-chidoan/chidoantruong-quan-ly-chidoan';
	
	
	protected $action = '';
	protected $actionParams = [];
	
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
	
	public function setAction($action) {
		$this->action = $action;
	}
	
	public function getTemplate($name) {
		if($name === 'list') {
			if($this->action === 'chia-doi-thieu-nhi') {
				return 'admin/chi-doan/list-chia-doi-thieu-nhi.html.twig';
			} elseif($this->action === 'chia-truong-phu-trach') {
				return 'admin/chi-doan/list-chia-truong-phu-trach.html.twig';
				
			}
			
			return 'admin/chi-doan/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	
	public function configureRoutes(RouteCollection $collection) {
		$collection->add('thieuNhiChiDoanChiaDoi', $this->getRouterIdParameter() . '/thieu-nhi/chia-doi');
		$collection->add('thieuNhiChiDoanChiaTruongPhuTrach', $this->getRouterIdParameter() . '/thieu-nhi/chia-truong-phu-trach');
		
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$action       = $this->action;
		$actionParams = $this->actionParams;
		
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id');
	}
	
	/**
	 * @param string    $name
	 * @param ThanhVien $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		/** @var ContainerInterface $container */
		$container = $this->configurationPool->getContainer();
		
		if($this->isAdmin()) {
			return true;
		}
		
		if($name === 'DELETE') {
			return false;
		}
		
		$user = $container->get(UserService::class)->getUser();
		
		$thanhVien = $this->getUserThanhVien();
		if(empty($thanhVien) || ! $thanhVien->isEnabled()) {
			return $this->isAdmin();
		}
		if($thanhVien->isBQT()) {
			if($name === 'chia-doi-thieu-nhi') {
				return false;
			}
			
			return true;
		}
		
		if($name === 'chia-doi-thieu-nhi' || $name === 'chia-truong-phu-trach') {
			if(empty($phanBoNamNay = $thanhVien->getPhanBoNamNay())) {
				return false;
			}
			if(empty($phanBoNamNay->getNamHoc()->isEnabled())) {
				return false;
			}
			if($phanBoNamNay->isChiDoanTruong()) {
				if($phanBoNamNay->getChiDoan()->getId() === $object->getId()) {
					return true;
				}
			}
			
			return false;
		}
		
		if($name === 'LIST') {
			return true;
		}
		
		return false;


//		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query = parent::createQuery($context);
		/** @var QueryBuilder $qb */
		$qb        = $query->getQueryBuilder();
		$expr      = $qb->expr();
		$rootAlias = $qb->getRootAliases()[0];
		if($this->action === 'list-thieu-nhi') {
			$this->clearResults($query);
		}
		if($this->action === 'chia-doi-thieu-nhi') {
			$this->clearResults($query);
		}
		
		return $query;
	}
	
	public function generateUrl($name, array $parameters = array(), $absolute = UrlGeneratorInterface::ABSOLUTE_PATH) {
		if($this->action === 'list-thieu-nhi') {
			if($name === 'list') {
				$name = 'thieuNhi';
			}
		}
		
		return parent::generateUrl($name, $parameters, $absolute);
	}
	
	protected function configureListFields(ListMapper $listMapper) {
		$danhSachChiDoan = [
			'Chiên Con 4 tuổi'    => 4,
			'Chiên Con 5 tuổi'    => 5,
			'Chiên Con 6 tuổi'    => 6,
			'Ấu Nhi 7 tuổi'       => 7,
			'Ấu Nhi 8 tuổi'       => 8,
			'Ấu Nhi 9 tuổi'       => 9,
			'Thiếu Nhi 10 tuổi'   => 10,
			'Thiếu Nhi 11 tuổi'   => 11,
			'Thiếu Nhi 12 tuổi'   => 12,
			'Nghĩa Sĩ 13 tuổi'    => 13,
			'Nghĩa Sĩ 14 tuổi'    => 14,
			'Nghĩa Sĩ 15 tuổi'    => 15,
			'Tông Đồ 16 tuổi'     => 16,
			'Tông Đồ 17 tuổi'     => 17,
			'Tông Đồ 18 tuổi'     => 18,
			'Dự Trưởng (19 tuổi)' => 19,
		];
		
		$listMapper
//			->addIdentifier('id')
			->addIdentifier('id', null, array())
//			->add('thanhVien', null, array( 'associated_property' => 'name' ))
			->add('thanhVien.christianName', null, array(
				'label' => 'list.label_christianname'
			))
			->add('thanhVien.lastName', null, array(
				'label' => 'list.label_lastname'
			))
			->add('thanhVien.middleName', null, array(
				'label' => 'list.label_middlename'
			))
			->add('thanhVien.firstName', null, array(
				'label' => 'list.label_firstname'
			));
		if($this->action === 'chia-doi-thieu-nhi') {
			$listMapper->add('phanBoTruoc.bangDiem', null, array( 'associated_property' => 'tbYear' ));
		}
		$listMapper->add('chiDoan', null, array(
			'associated_property' => 'number'
		));
	}
	
	protected function configureFormFields(FormMapper $formMapper) {
		$isAdmin   = $this->isAdmin();
		$container = $this->getConfigurationPool()->getContainer();
		
		$danhSachChiDoan = [
			'Chiên Con 4 tuổi'    => 4,
			'Chiên Con 5 tuổi'    => 5,
			'Chiên Con 6 tuổi'    => 6,
			'Ấu Nhi 7 tuổi'       => 7,
			'Ấu Nhi 8 tuổi'       => 8,
			'Ấu Nhi 9 tuổi'       => 9,
			'Thiếu Nhi 10 tuổi'   => 10,
			'Thiếu Nhi 11 tuổi'   => 11,
			'Thiếu Nhi 12 tuổi'   => 12,
			'Nghĩa Sĩ 13 tuổi'    => 13,
			'Nghĩa Sĩ 14 tuổi'    => 14,
			'Nghĩa Sĩ 15 tuổi'    => 15,
			'Tông Đồ 16 tuổi'     => 16,
			'Tông Đồ 17 tuổi'     => 17,
			'Tông Đồ 18 tuổi'     => 18,
			'Dự Trưởng (19 tuổi)' => 19,
		];
		
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general')//            ->add('children')
		;
		$formMapper
			->add('cotDiemBiLoaiBo', ChoiceType::class, array(
				'label'              => 'list.label_cot_diem_bi_loai_bo',
				'multiple'           => true,
				'placeholder'        => 'Chọn Cot Diem',
				'choices'            => [
					'Chuyên cần T9'  => 'cc9',
					'Chuyên cần T10' => 'cc10',
					'Chuyên cần T11' => 'cc11',
					'Chuyên cần T12' => 'cc12',
					'Chuyên cần T1'  => 'cc1',
					'Chuyên cần T2'  => 'cc2',
					'Chuyên cần T3'  => 'cc3',
					'Chuyên cần T4'  => 'cc4',
					'Chuyên cần T5'  => 'cc5',
					'TB Miệng HK-1'  => 'quizTerm1',
					'TB Miệng HK-2'  => 'quizTerm2',
					
					'Giữa HK-1' => 'midTerm1',
					'Thi HK-1'  => 'finalTerm1',
					'Giữa HK-2' => 'midTerm2',
					'Thi HK-2'  => 'finalTerm2'
				],
				'translation_domain' => $this->translationDomain
			));
		
		$formMapper
			->end()
			->end();
		
	}
	
	/**
	 * @param ChiDoan $object
	 */
	public function preValidate($object) {
	
	}
}