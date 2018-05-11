<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ChristianName;
use App\Entity\HoSo\PhanBo;
use App\Service\HoSo\ThanhVien\HuynhTruongService;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Doctrine\ORM\Query\Expr;
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
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\BooleanType;
use Sonata\CoreBundle\Form\Type\DatePickerType;
use Sonata\CoreBundle\Form\Type\EqualType;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ThieuNhiAdmin extends BaseAdmin {
	
	const ENTITY = ThanhVien::class;
	
	protected $baseRouteName = 'admin_app_hoso_thanhvien_thieunhi';
	
	protected $baseRoutePattern = '/app/hoso-thanhvien/thieunhi';
	
	protected $datagridValues = array(
		// display the first page (default = 1)
		'_page'       => 1,
		
		// reverse order (default = 'ASC')
		'_sort_order' => 'DESC',
		
		// name of the ordered field (default = the model's id field, if any)
		'_sort_by'    => 'updatedAt',
		
		'enabled' => array(
			'type'  => EqualType::TYPE_IS_EQUAL, // => 1
			'value' => BooleanType::TYPE_YES     // => 1
		)
	);
	
	
	/**
	 * @var integer
	 */
	protected $namHoc;
	
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
			
			return 'admin/thieu-nhi/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
//		$collection->add('employeesImport', $this->getRouterIdParameter() . '/import');
		$collection->add('thieuNhiNhom', 'nhom-giao-ly-cua-truong/{phanBo}');
		$collection->add('thieuNhiChiDoan', 'chi-doan/{phanBo}/list');
		$collection->add('thieuNhiPhanDoan', '{phanDoan}');
		
		$collection->add('sanhHoatLai', '' . $this->getRouterIdParameter() . '/sanh-hoat-lai');
		$collection->add('nghiSanhHoat', '' . $this->getRouterIdParameter() . '/nghi-sanh-hoat');
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id', null, array( 'label' => 'list.label_id' ))
			->add('name', null, array( 'label' => 'list.label_name', 'show_filter' => true ));
		if( ! in_array($this->action, [ 'list-thieu-nhi-nhom', 'list-thieu-nhi-chi-doan' ])) {
			$datagridMapper->add('chiDoan', null, array( 'label' => 'list.label_chi_doan', 'show_filter' => true ));
		}
//		$datagridMapper->add('namHoc', null, array( 'label' => 'list.label_nam_hoc', 'show_filter' => true ));
		$datagridMapper
			->add('ngheoKho', null, array(
				'label'       => 'list.label_ngheo_kho',
				'show_filter' => true,
				'default'     => true
			))
			->add('dacBiet', null, array(
				'label'       => 'list.label_dac_biet',
				'show_filter' => true,
				'default'     => true
			));
		$datagridMapper->add('enabled', null, array(
			'label'       => 'list.label_active',
			'show_filter' => true,
			'default'     => true
		));
	}
	
	/**
	 * @param string    $name
	 * @param ThanhVien $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		$this->getAction();
		$container = $this->getConfigurationPool()->getContainer();
		
		$tv = $thanhVien = $this->getUserThanhVien();
		
		if(empty($tv) || ! $tv->isEnabled()) {
			return $this->isAdmin();
		}
		
		if( ! empty($object)) {
			$phanBoNamNay = $object->getPhanBoNamNay();
		}
		
		if($name === 'sanh-hoat-lai') {
			if(empty($thanhVien)) {
				return $this->isAdmin();
			}
			
			if(empty($object)) {
				return false;
			}
			if($object->isEnabled()) {
				return false;
			}
			
			if( ! empty($thuKyChiDoan = $thanhVien->getThuKyChiDoanObj()) && $thuKyChiDoan->isThieuNhiCungChiDoan($object->getPhanBoNamNay())) {
				return true;
			}
			
			return ! empty($thanhVien->isCDTorGreater($object));
		} elseif($name === 'nghi-sanh-hoat') {
			if(empty($thanhVien)) {
				return $this->isAdmin();
			}
			
			if(empty($object)) {
				return false;
			}
			if( ! $object->isEnabled()) {
				return false;
			}
			
			return ! empty($thanhVien->isTruongPTorGreater($object));
		}
		
		if($name === 'xet-len-lop') {
			
			if(empty($thanhVien)) {
				return $this->isAdmin();
			}
			
			if(empty($object)) {
				return false;
			}
			
			if( ! empty($phanBoNamNay)) {
				$bangDiem = $phanBoNamNay->createBangDiem();
				
				if(empty($bangDiem->isGradeRetention())) {
					return false;
				}
			}
			
			if(($permission = $thanhVien->isCDTorGreater($object)) !== null) {
				return $permission;
			}
			
			$phanBo    = $thanhVien->getPhanBoNamNay();
			$cacTruong = $phanBo->getCacTruongPhuTrachDoi();
			/** @var TruongPhuTrachDoi $truong */
			foreach($cacTruong as $truong) {
				$doiNhomGiaoLy = $truong->getDoiNhomGiaoLy();
				/** @var PhanBo $_phanBoTN */
				foreach($doiNhomGiaoLy->getPhanBoThieuNhi() as $_phanBoTN) {
					if($_phanBoTN === $object) {
						return true;
					}
				}
			}
			
			return false;
		}
		
		if($this->isAdmin()) {
			return true;
		}
		
		if($name === 'DELETE') {
			return false;
		}
		
		if($thanhVien->isBQT()) {
			return true;
		}
		
		if( ! $thanhVien->isEnabled()) {
			return false;
		}
		
		if($name === 'LIST' || $name === 'VIEW') {
			return true;
		}
		
		if($name === 'CREATE') {
			return $thanhVien->isCDTorGreater($object);
		}
		
		if(in_array($this->action, [ 'truong-chi-doan', 'list-thieu-nhi-nhom' ]) || $name === 'EDIT') {
			if($this->action === 'truong-chi-doan') {
				if( ! empty($thanhVien->getPhanBoNamNay()->isChiDoanTruong())) {
					if($name === 'EDIT') {
						if(empty($object)) {
							return false;
						}
						
						return ($object->getPhanBoNamNay()->getChiDoan() === $thanhVien->getPhanBoNamNay()->getChiDoan());
					}
					
					return true;
				}
				
				return false;
			} elseif(in_array($this->action, [ 'list-thieu-nhi-nhom', 'list-thieu-nhi-chi-doan' ])) {
				if($name === 'EXPORT') {
					return true;
				}
			}
			
			if($name === 'EDIT') {
//					if($thanhVien->isChiDoanTruong()) {
//						return true;
//					}
				if(empty($object)) {
					return false;
				}
				
				if( ! empty($phanBoNamNay)) {
					$doiNhomGiaoLy = $phanBoNamNay->getDoiNhomGiaoLy();
				} else {
					return false;
				}
				
				if(empty($doiNhomGiaoLy)) {
					return $thanhVien->isCDTorGreater($object);
				}
				
				$cacTruongPT = $doiNhomGiaoLy->getCacTruongPhuTrachDoi();
				/** @var TruongPhuTrachDoi $item */
				foreach($cacTruongPT as $item) {
					if($item->getPhanBo()->getThanhVien()->getId() === $thanhVien->getId()) {
						return true;
					}
				}
				
				return $thanhVien->isCDTorGreater($object);
			}
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
		
		$query->andWhere($expr->eq($rootAlias . '.huynhTruong', $expr->literal(false)));
		
		$query->andWhere($expr->eq($rootAlias . '.thieuNhi', $expr->literal(true)));
		
		if($this->action === 'list-thieu-nhi-nhom') {
			/** @var array $dngl */
			$cacDoiNhomGiaoLy = $this->actionParams['cacDoiNhomGiaoLy'];
			if(count($cacDoiNhomGiaoLy) > 0) {
				$dnglIds = [];
				/** @var DoiNhomGiaoLy $dngl */
				foreach($cacDoiNhomGiaoLy as $dngl) {
					$dnglIds[] = $dngl->getId();
				}
				$qb->join($rootAlias . '.phanBoHangNam', 'phanBo');
				$qb->join('phanBo.doiNhomGiaoLy', 'doiNhomGiaoLy');
				
				$query->andWhere($expr->in('doiNhomGiaoLy.id', $dnglIds));
			} else {
				$this->clearResults($query);
			}
		}
//		elseif($this->action === 'list-thieu-nhi-chi-doan') {
//			$query->andWhere($expr->eq($rootAlias . '.chiDoan', $chiDoan->getNumber()));
////			$query->andWhere($expr->eq($rootAlias . '.namHoc', $chiDoan->getNamHoc()->getId()));
//		}else		
		elseif(in_array($this->action, [ 'list-thieu-nhi-chi-doan', 'list-thieu-nhi-phan-doan' ])) {
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
				$name = 'thieuNhiNhom';
				if(array_key_exists('phanBo', $this->actionParams)) {
					$phanBoId = $this->actionParams['phanBo']->getId();
				} else {
					$phanBoId = $this->getRequest()->query->get('phanBoId');
				}
				$parameters['phanBo'] = $phanBoId;
			} elseif($this->action === 'list-thieu-nhi-chi-doan') {
				$name = 'thieuNhiChiDoan';
				if(array_key_exists('phanBo', $this->actionParams)) {
					$phanBoId = $this->actionParams['phanBo']->getId();
				} else {
					$phanBoId = $this->getRequest()->query->get('phanBoId');
				}
				$parameters['phanBo'] = $phanBoId;
			} elseif($this->action === 'list-thieu-nhi-phan-doan') {
				$name                   = 'thieuNhiPhanDoan';
				$parameters['phanDoan'] = $this->actionParams['phanDoan'];
			}
		} elseif($name === 'edit') {
		
		}
		
		return parent::generateUrl($name, $parameters, $absolute);
	}
	
	public function getPersistentParameters() {
		$parameters = parent::getPersistentParameters();
		if( ! $this->hasRequest() || empty($this->action)) {
			return $parameters;
		}
		if(array_key_exists('phanBo', $this->actionParams)) {
			$phanBoId = $this->actionParams['phanBo']->getId();
		} else {
			$phanBoId = $this->getRequest()->query->get('phanBoId');
		}
		
		return array_merge($parameters, array(
			'action'   => $this->action,
			'phanBoId' => $phanBoId
		));
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
//			->addIdentifier('code')
//			->addIdentifier('christianname', null, array())
			->addIdentifier('name', null, array())
//			->addIdentifier('lastname', null, array())
//			->addIdentifier('middlename', null, array())
//			->addIdentifier('firstname', null, array())
			->addIdentifier('hoTenBo', null, array( 'label' => 'list.label_hoten_bo' ))
			->addIdentifier('hoTenMe', null, array( 'label' => 'list.label_hoten_me' ));
		if( ! empty($thanhVien = $this->getUserThanhVien()) && $thanhVien->isBQT()) {
			$listMapper
				->add('dob', null, array( 'editable' => true ))
				->add('soDienThoaiBo', null, array(
					'label'    => 'list.label_so_dien_thoai_bo',
					'editable' => true
				))
				->add('soDienThoaiMe', null, array(
					'label'    => 'list.label_so_dien_thoai_me',
					'editable' => true
				))
				->add('soDienThoai', null, array(
					'label'    => 'list.label_so_dien_thoai',
					'editable' => true
				))
				->add('soDienThoaiSecours', null, array(
					'label'    => 'list.label_so_dien_thoai_secours',
					'editable' => true
				))
				->add('diaChiThuongTru', null, array(
					'label'    => 'list.label_dia_chi',
					'editable' => true
				));
		}
		
		if($this->action !== 'list-thieu-nhi-nhom') {
			$listMapper->add('_nhomGiaoLy', 'actions', array(
				'template' => 'admin/thieu-nhi/list__field__doi_giao_ly.html.twig'
			));
			
			if( ! in_array($this->action, [ 'list-thieu-nhi-chi-doan' ])) {
				$listMapper->add('chiDoan', 'choice', array(
					'editable' => false,
//				'class' => 'Vendor\ExampleBundle\Entity\ExampleStatus',
					'choices'  => $danhSachChiDoan,
				));
			}
		}
		$listMapper->add('namHoc', 'text', array( 'editable' => false ));
		$listMapper->add('ngheoKho')->add('dacBiet');
		$listMapper->add('enabled', null, array( 'editable' => false, 'label' => 'list.label_active' ))
		           ->add('_action', 'actions', array(
			           'actions' => array(
				           'edit'           => array(),
//					'delete' => array(),
				           'xet_len_lop'    => array( 'template' => 'admin/thieu-nhi/list__action__xet_len_lop.html.twig' ),
				           'sanh_hoat_lai'  => array( 'template' => 'admin/thieu-nhi/list__action__sanh_hoat_lai.html.twig' ),
				           'nghi_sanh_hoat' => array( 'template' => 'admin/thieu-nhi/list__action__nghi_sanh_hoat.html.twig' )

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
		
		$user      = $container->get(UserService::class)->getUser();
		$thanhVien = $user->getThanhVien();
		
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
			->with('form.group_lien_lac', [ 'class' => 'col-md-3' ])->end()
			->with('form.group_gia_dinh', [ 'class' => 'col-md-3' ])->end()
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
			->add('notes', CKEditorType::class, array(
				'required' => false,
				'label'    => 'list.label_notes'
			));
		
		$formMapper
			->end();
		
		$formMapper->with('form.group_extra');
		if($thanhVien->isBQT()) {
			$formMapper->add('phanDoan', ChoiceType::class, array(
				'label'              => 'list.label_phan_doan',
				'placeholder'        => 'Chọn Phân Đoàn',
				'choices'            => ThanhVien::$danhSachPhanDoan,
				'translation_domain' => $this->translationDomain,
				'required'           => false
			));
		}
		if($thanhVien->isBQT() || $thanhVien->isPhanDoanTruongOrSoeur()) {
			$formMapper->add('chiDoan', ChoiceType::class, array(
				'required'           => false,
				'label'              => 'list.label_chi_doan',
				'placeholder'        => 'Chọn Chi Đoàn',
				'choices'            => $danhSachChiDoan,
				'translation_domain' => $this->translationDomain
			));
		}
		$formMapper->add('ngheoKho', null, array(
			'label' => 'list.label_ngheo_kho',
		))
		           ->add('dacBiet', null, array(
			           'label' => 'list.label_dac_biet',
		           ))
		           ->add('enabled', null, array(
			           'label' => 'list.label_enabled',
		           ));
		$formMapper->end();
		
		$formMapper->with('form.group_lien_lac')
		           ->add('soDienThoai', null, array( 'required' => false, 'label' => 'list.label_so_dien_thoai', ))
		           ->add('soDienThoaiSecours', null, array(
			           'label'    => 'list.label_so_dien_thoai_secours',
			           'required' => false
		           ))
		           ->add('diaChiThuongTru', null, array(
			           'required' => false,
			           'label'    => 'list.label_dia_chi_thuong_tru',
		           ))
		           ->add('diaChiTamTru', null, array(
			           'required' => false,
			           'label'    => 'list.label_dia_chi_tam_tru',
		           ));
		$formMapper->end();
		
		$pool    = $this->getConfigurationPool();
		$request = $this->getRequest();
		/** @var QueryBuilder $parentQuery */
		$tenThanhMeQuery = $this->getModelManager()->createQuery(ChristianName::class);
		/** @var Expr $expr */
		$expr = $tenThanhMeQuery->expr();
//				$sql = $childrenQuery->getQuery()->getSQL();
		$rootAlias = $tenThanhMeQuery->getRootAliases()[0];
		$tenThanhMeQuery->andWhere($expr->eq($rootAlias . '.sex', $expr->literal('NỮ')));
		
		
		/** @var QueryBuilder $parentQuery */
		$tenThanhBoQuery = $this->getModelManager()->createQuery(ChristianName::class);
		/** @var Expr $expr */
//				$sql = $childrenQuery->getQuery()->getSQL();
		$rootAlias = $tenThanhBoQuery->getRootAliases()[0];
		$tenThanhBoQuery->andWhere($expr->eq($rootAlias . '.sex', $expr->literal('NAM')));
		
		$formMapper->with('form.group_gia_dinh')
		           ->add('tenThanhMe', ModelType::class, array(
			           'required' => false,
			           'label'    => 'list.label_christianname_mom',
			           'property' => 'tiengViet',
			           'query'    => $tenThanhMeQuery
		           ))
		           ->add('hoTenMe', null, array( 'required' => false, 'label' => 'list.label_hoten_me', ))
		           ->add('soDienThoaiMe', null, array( 'required' => false, 'label' => 'list.label_sdt_me', ))
		           ->add('tenThanhBo', ModelType::class, array(
			           'required' => false,
			           'label'    => 'list.label_christianname_dad',
			           'property' => 'tiengViet',
			           'query'    => $tenThanhBoQuery
		           ))
		           ->add('hoTenBo', null, array( 'required' => false, 'label' => 'list.label_hoten_bo', ))
		           ->add('soDienThoaiBo', null, array( 'required' => false, 'label' => 'list.label_sdt_bo', ))
		           ->add('soAnhChiEm', IntegerType::class, array(
			           'required' => false,
			           'label'    => 'list.label_so_ace',
		           ));
		$formMapper->end();
		
		$formMapper->end();
		
		
	}
	
	protected function configureShowFields(ShowMapper $show) {
		parent::configureShowFields($show);
		$tv        = $this->getUserThanhVien();
		$showField = false;
		if($tv->isBQT() || $tv->isPhanDoanTruongOrSoeur()) {
			$showField = true;
		} else {
			$phanBoNamNay = $tv->getPhanBoNamNay();
			$cacTruong    = $phanBoNamNay->getCacTruongPhuTrachDoi();
			/** @var TruongPhuTrachDoi $truong */
			foreach($cacTruong as $truong) {
				/** @var ThanhVien $subject */
				$subject        = $this->getSubject();
				$phanBoThieuNhi = $subject->getPhanBoNamNay();
				$nhomGL         = $phanBoThieuNhi->getDoiNhomGiaoLy();
				if( ! empty($nhomGL) && ! empty($truong->getDoiNhomGiaoLy()) && $nhomGL->getId() === $truong->getDoiNhomGiaoLy()->getId()) {
					$showField = true;
				}
			}
		}
		
		
		$show
			->tab('form.tab_info')
			->with('form.group_general', [ 'class' => 'col-md-6' ])->end()
			->with('form.group_extra', [ 'class' => 'col-md-6' ])->end()
			->with('form.group_lien_lac', [ 'class' => 'col-md-3' ])->end()
			->with('form.group_gia_dinh', [ 'class' => 'col-md-3' ])->end()
			//->with('form.group_job_locations', ['class' => 'col-md-4'])->end()
			->end();
		$show
			->tab('form.tab_info')
			->with('form.group_general');
		
		$show
			->add('tenThanh', ModelType::class, array(
				'required'            => false,
				'label'               => 'list.label_christianname',
				'associated_property' => 'tiengViet'
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
			->add('notes', CKEditorType::class, array(
				'required' => false,
				'label'    => 'list.label_notes'
			));
		
		$show
			->end();
		
		$show->with('form.group_extra')
		     ->add('phanDoan', null, array( 'label' => 'list.label_phan_doan' ))
		     ->add('chiDoan', null, array( 'label' => 'list.label_chi_doan' ))
		     ->add('ngheoKho', null, array(
			     'label' => 'list.label_ngheo_kho',
		     ))
		     ->add('dacBiet', null, array(
			     'label' => 'list.label_dac_biet',
		     ))
		     ->add('enabled', null, array(
			     'label' => 'list.label_active',
		     ));
		$show->end();
		
		if($showField) {
			$show->with('form.group_lien_lac')
			     ->add('soDienThoai', null, array( 'label' => 'list.label_so_dien_thoai', ))
			     ->add('soDienThoaiSecours', null, array(
				     'label' => 'list.label_so_dien_thoai_secours'
			     ))
			     ->add('diaChiThuongTru', null, array(
				     'label' => 'list.label_dia_chi_thuong_tru',
			     ))
			     ->add('diaChiTamTru', null, array(
				     'label' => 'list.label_dia_chi_tam_tru',
			     ));
			$show->end();
			
			$show->with('form.group_gia_dinh')
			     ->add('tenThanhMe', ModelType::class, array(
				     'label'               => 'list.label_christianname_mom',
				     'associated_property' => 'tiengViet'
			     ))
			     ->add('hoTenMe', null, array( 'label' => 'list.label_hoten_me', ))
			     ->add('soDienThoaiMe', null, array( 'label' => 'list.label_sdt_me', ))
			     ->add('tenThanhBo', ModelType::class, array(
				     'label'               => 'list.label_christianname_dad',
				     'associated_property' => 'tiengViet'
			     ))
			     ->add('hoTenBo', null, array( 'label' => 'list.label_hoten_bo', ))
			     ->add('soDienThoaiBo', null, array( 'label' => 'list.label_sdt_bo', ))
			     ->add('soAnhChiEm', IntegerType::class, array( 'label' => 'list.label_so_ace', ));
			$show->end();
			
			$show->end();
		}
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function preValidate($object) {
		$this->getConfigurationPool()->getContainer()->get(HuynhTruongService::class)->addThieuNhi($this->getUserThanhVien(), $object);
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function prePersist($object) {
	
	}
	
	/**
	 * @param ThanhVien $object
	 */
	public function postPersist($object) {
		$object->setCode(strtoupper(User::generate4DigitCode($object->getId())));
		$this->getModelManager()->update($object);
	}
	
	/**
	 * @return int
	 */
	public function getNamHoc() {
		return $this->namHoc;
	}
	
	/**
	 * @param int $namHoc
	 */
	public function setNamHoc($namHoc) {
		$this->namHoc = $namHoc;
	}
	
	
}