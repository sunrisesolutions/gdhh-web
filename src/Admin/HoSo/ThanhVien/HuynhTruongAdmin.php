<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Service\HoSo\NamHocService;
use App\Service\HoSo\ThanhVienService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Form\Type\ModelType;
use App\Admin\BaseAdmin;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Exception\Exception;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelAutocompleteType;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HuynhTruongAdmin extends BaseAdmin {
	const ENTITY = ThanhVien::class;
	
	protected $baseRouteName = 'admin_app_hoso_thanhvien_huynhtruong';
	
	protected $baseRoutePattern = '/app/hoso-thanhvien/huynhtruong';
	
	protected $datagridValues = array(
		// display the first page (default = 1)
		'_page'       => 1,
		
		// reverse order (default = 'ASC')
		'_sort_order' => 'DESC',
		
		// name of the ordered field (default = the model's id field, if any)
//		'_sort_by'    => 'updatedAt',
	);
	
	protected $translationDomain = 'BinhLeAdmin';
	
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
			if($this->action === 'list-thieu-nhi') {
				return 'admin/huynh-truong/list-thieu-nhi.html.twig';
			}
//			if($this->action === 'list-thieu-nhi-nhom') {
//				return 'admin/huynh-truong/list-thieu-nhi-nhom.html.twig';
//			}
			
			return 'admin/huynh-truong/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	
	public function configureRoutes(RouteCollection $collection) {
//		$collection->add('employeesImport', $this->getRouterIdParameter() . '/import');
		$collection->add('import', 'import/{namHoc}');
		$collection->add('truongChiDoan', 'truong/chi-doan-{chiDoan}');
		$collection->add('truongPhanDoan', 'truong/{phanDoan}');
		
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id', null, array( 'label' => 'list.label_id' ))
			->add('name', null, array( 'label' => 'list.label_name', 'show_filter' => true ))
			->add('chiDoan', null, array( 'label' => 'list.label_chi_doan', 'show_filter' => true ))
			->add('namHoc', null, array( 'label' => 'list.label_nam_hoc', 'show_filter' => true ))
			->add('enabled', null, array( 'label' => 'list.label_active', 'show_filter' => true ));
	}
	
	/**
	 * @param string    $name
	 * @param ThanhVien $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		$container = $this->getConfigurationPool()->getContainer();
		
		$tv = $thanhVien = $this->getUserThanhVien();
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if($name === 'DELETE') {
			return false;
		}
		
		$user = $container->get(UserService::class)->getUser();
		if($thanhVien->isBQT()) {
			return true;
		}
		
		if($thanhVien->isPhanDoanTruongOrSoeur($object)) {
			return true;
		}
		
		if($name === 'LIST' || $name === 'VIEW') {
			return true;
		}
		
		if($name === 'CREATE') {
			return false;
		}
		
		if(($isGranted = $thanhVien->isGranted($name, $this->action, $object) !== null)) {
			return $isGranted;
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
		
		
		$query->andWhere($expr->eq($rootAlias . '.huynhTruong', $expr->literal(true)));
		$tv = $this->getUserThanhVien();
		if($tv->isPhanDoanTruongOrSoeur()) {
			$qb->andWhere($expr->eq($rootAlias . '.phanDoan', $expr->literal($tv->getPhanDoan())));
		}
		
		if(in_array($this->action, [ 'truong-chi-doan', 'truong-phan-doan' ])) {
			$qb->join($rootAlias . '.phanBoHangNam', 'phanBo');
			$qb->join('phanBo.chiDoan', 'chiDoan');
			if(array_key_exists('chiDoan', $this->actionParams)) {
				$qb->andWhere($expr->eq('chiDoan.id', $expr->literal($this->actionParams['chiDoan']->getId())));
			}
			if(array_key_exists('danhSachChiDoan', $this->actionParams)) {
				$qb->andWhere($expr->in('chiDoan.id', ':danhSachChiDoan'))
				   ->setParameter('danhSachChiDoan', $this->actionParams['danhSachChiDoan']);
			}
			
		}
		
		return $query;
	}
	
	public function generateUrl($name, array $parameters = array(), $absolute = UrlGeneratorInterface::ABSOLUTE_PATH) {
		if($name === 'list') {
			if($this->action === 'list-thieu-nhi') {
				$name = 'thieuNhi';
			} elseif($this->action === 'list-thieu-nhi-nhom') {
				$name                 = 'thieuNhiNhom';
				$parameters['phanBo'] = $this->actionParams['phanBo']->getId();
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
			->addIdentifier('id')
//			->addIdentifier('christianname', null, array())
			->addIdentifier('name', null, array())
			->add('dob', null, array( 'editable' => true ))
			->add('soDienThoai', null, array(
				'label'    => 'list.label_so_dien_thoai',
				'editable' => true
			))
			->add('soDienThoaiSecours', null, array(
				'label'    => 'list.label_so_dien_thoai_secours',
				'editable' => true
			))
			->add('diaChiThuongTru', null, array(
				'label'    => 'list.label_dia_chi_thuong_tru',
				'editable' => true
			))
			->add('chiDoan', 'choice', array(
				'editable' => false,
//				'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
				'choices'  => $danhSachChiDoan,
			))
			->add('namHoc', 'text', array( 'editable' => false ))
			->add('enabled', null, array( 'editable' => false, 'label' => 'list.label_active' ))
			->add('_action', 'actions', array(
				'actions' => array(
					'edit' => array(),
//					'delete' => array(),
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
		
		$thanhVien = $this->getUserThanhVien();
		
		if($thanhVien->isPhanDoanTruongOrSoeur()) {
			$phanDoan = $thanhVien->getPhanDoan();
			switch($phanDoan) {
				case ThanhVien::PHAN_DOAN_CHIEN:
					$danhSachChiDoan = [
						'Chiên Con 4 tuổi' => 4,
						'Chiên Con 5 tuổi' => 5,
						'Chiên Con 6 tuổi' => 6
					];
					break;
				case ThanhVien::PHAN_DOAN_AU:
					$danhSachChiDoan = [
						'Ấu Nhi 7 tuổi' => 7,
						'Ấu Nhi 8 tuổi' => 8,
						'Ấu Nhi 9 tuổi' => 9
					];
					break;
				case ThanhVien::PHAN_DOAN_THIEU:
					$danhSachChiDoan = [
						'Thiếu Nhi 10 tuổi' => 10,
						'Thiếu Nhi 11 tuổi' => 11,
						'Thiếu Nhi 12 tuổi' => 12,
					];
					break;
				case ThanhVien::PHAN_DOAN_NGHIA:
					$danhSachChiDoan = [
						'Nghĩa Sĩ 13 tuổi' => 13,
						'Nghĩa Sĩ 14 tuổi' => 14,
						'Nghĩa Sĩ 15 tuổi' => 15,
					];
					break;
				case ThanhVien::PHAN_DOAN_TONG_DO:
					$danhSachChiDoan = [
						'Tông Đồ 16 tuổi' => 16,
						'Tông Đồ 17 tuổi' => 17,
						'Tông Đồ 18 tuổi' => 18,
					];
					break;
			}
			
		} else {
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
		}
		
		/** @var ThanhVien $subject */
		if( ! empty($subject = $this->getSubject())) {
			$christianName = $subject->getChristianName();
			if(array_key_exists($christianName, ThanhVien::$christianNames)) {
				$christianName = ThanhVien::$christianNames[ $christianName ];
			}
		} else {
			$christianName = '';
		}
		
		// define group zoning
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general', [ 'class' => 'col-md-6' ])->end()
			->with('form.group_extra', [ 'class' => 'col-md-6' ])->end()
			//->with('form.group_job_locations', ['class' => 'col-md-4'])->end()
			->end();

//			->tab('form.tab_layout_grid')
//			->with('form.group_column', [ 'class' => 'col-md-7' ])->end()
//			->with('form.group_row', [ 'class' => 'col-md-5' ])->end()
//			->end()
//			->tab('form.tab_layout_inline')
//			->with('form.group_general', [ 'class' => 'col-md-12' ])->end()
		//->with('form.group_job_locations', ['class' => 'col-md-4'])->end()
//			->end();
		
		
		$formMapper
			->tab('form.tab_info')
			->with('form.group_general');
		if($this->isAdmin()) {
//			$formMapper
//				->add('user', ModelAutocompleteType::class, array(
//					'property' => 'username'
//				,
//					'required' => false,
//				));
		}
		if(empty($subject->getId())) {
			$subject->setUser(new User());
			$formMapper
				->add('user.username', null, array(
					'required' => true,
					'label'    => 'list.label_username'
				));
		}
		$formMapper
			->add('user.email', null, array(
				'required' => true,
				'label'    => 'list.label_email_address'
			));
		$formMapper
			->add('tenThanh', ModelType::class, array(
				'required' => false,
				'label'    => 'list.label_christianname',
				'property' => 'tiengViet'
			))
//			->add('christianname', ChoiceType::class, array(
//				'label'              => 'list.label_christianname',
//				'placeholder'        => 'Chọn Tên Thánh',
//				'required'           => false,
//				'choices'            => ThanhVien::$christianNames,
//				'data'               => $christianName,
//				'translation_domain' => $this->translationDomain
//			))
			->add('lastname', null, array(
				'label' => 'list.label_lastname',
			))
			->add('middlename', null, array(
				'label' => 'list.label_middlename',
			))
			->add('firstname', null, array(
				'label' => 'list.label_firstname',
			))
			->add('dob', DatePickerType::class, array(
				'format'   => 'dd/MM/yyyy',
				'required' => false,
				'label'    => 'list.label_dob'
			))
			->add('soDienThoai', null, array( 'required' => false, 'label' => 'list.label_so_dien_thoai', ))
			->add('soDienThoaiSecours', null, array(
				'label'    => 'list.label_so_dien_thoai_secours',
				'required' => false
			))
//			->add('soDienThoaiMe', null, array( 'required' => false,  ))
//			->add('soDienThoaiBo', null, array( 'required' => false ))
			->add('diaChiThuongTru', null, array( 'required' => false, 'label' => 'list.label_dia_chi_thuong_tru', ));
		
		$formMapper
			->end();
		
		$formMapper->with('form.group_extra');
		if($thanhVien->isBQT()) {
			$formMapper->add('phanDoan', ChoiceType::class, array(
				'label'              => 'list.label_phan_doan',
				'placeholder'        => 'Chọn Phân Đoàn',
				'choices'            => ThanhVien::$danhSachPhanDoan,
				'translation_domain' => $this->translationDomain,
				'required'           => true
			));
		}
		$formMapper->add('chiDoan', ChoiceType::class, array(
			'required'           => false,
			'label'              => 'list.label_chi_doan',
			'placeholder'        => 'Chọn Chi Đoàn',
			'choices'            => $danhSachChiDoan,
			'translation_domain' => $this->translationDomain
		))
			->add('chiDoanTruong', null, array(
				'label' => 'list.label_chi_doan_truong',
			))
			->add('thuKyChiDoan', null, array(
				'label' => 'list.label_thu_ky_chi_doan',
			))
			
		;
		
		if($thanhVien->isBQT()) {
			$formMapper->add('phanDoanTruong', null, array(
				'label' => 'list.label_phan_doan_truong',
			))
			           ->add('thuQuyXuDoan', null, array(
				           'label' => 'list.label_thu_quy_xu_doan',
			           ))
			           ->add('thuKyXuDoan', null, array(
				           'label' => 'list.label_thu_ky_xu_doan',
			           ))
			           ->add('xuDoanPhoNoi', null, array(
				           'label' => 'list.label_xu_doan_pho_noi',
			           ))
			           ->add('xuDoanPhoNgoai', null, array(
				           'label' => 'list.label_xu_doan_pho_ngoai',
			           ))
			           ->add('xuDoanTruong', null, array(
				           'label' => 'list.label_xu_doan_truong',
			           ))
			           ->add('soeur', null, array(
				           'label' => 'list.label_soeur_tro_uy',
			           ))
			           ->add('enabled', null, array(
				           'label' => 'list.label_enabled',
			           ));
		}
		$formMapper->end()->end();
		
		
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function preValidate($object) {
		$object->setThieuNhi(false);
		$object->setHuynhTruong(true);
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function prePersist($object) {
		$container = $this->getConfigurationPool()->getContainer();
		$username  = $object->getUser()->getUsername();
		$object->getUser()->setEnabled(true);
		
		$user = $container->get(UserService::class)->addUserIfNotExist($object->getUser(), $username, [ 'username' => $username ]);
		
		$object->setUser($user);
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function postPersist($object) {
		$object->setCode(strtoupper(User::generate4DigitCode($object->getId())));

//		$namHocHienTai = $this->getConfigurationPool()->getContainer()->get('app.binhle_thieunhi_namhoc')->getNamHocHienTai();
//		$object->initiatePhanBo($namHocHienTai);
//		$this->getConfigurationPool()->getContainer()->get('doctrine.orm.default_entity_manager')->persist($object);
		$this->getConfigurationPool()->getContainer()->get(ThanhVienService::class)->preUpdate($object);
		
		$this->getModelManager()->update($object);
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function preUpdate($object) {
		$this->getConfigurationPool()->getContainer()->get(ThanhVienService::class)->preUpdate($object);

//		if( ! empty($phanBoNamNay = $object->getPhanBoNamNay())) {
//			$phanBoNamNay->setVaiTro();
//			if(empty($object->getChiDoan())) {
//				$phanBoNamNay->setChiDoan(null);
//			}
//
//			$namHocHienTai = $this->getConfigurationPool()->getContainer()->get(NamHocService::class)->getNamHocHienTai();
//			$object->initiatePhanBo($namHocHienTai);
//		};
	}
	
	
}