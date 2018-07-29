<?php

namespace App\Admin\HoSo\ChiDoan;

use App\Admin\BaseAdmin;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ThanhVien;
use Bean\Bundle\CoreBundle\Service\StringService;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

use Psr\Container\ContainerInterface;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class PhanDoanTruongChiDoanAdmin extends BaseAdmin {
	protected $baseRouteName = 'admin_app_hoso_chidoan_phandoantruong_chidoan';
	
	protected $baseRoutePattern = '/app/hoso-chidoan/phandoantruong-quan-ly-chidoan';
	
	const ENTITY = ChiDoan::class;
	
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
			if($this->action === 'bao-cao-tien-quy') {
				return 'admin/ban-quan-tri/chi-doan/list-bao-cao-tien-quy.html.twig';
			} elseif($this->action === 'duyet-bang-diem') {
			
			}
			return 'admin/phan-doan-truong/list.html.twig';
		}
		
		return parent::getTemplate($name);
	}
	
	public function configureRoutes(RouteCollection $collection) {
		$collection->add('baoCaoTienQuy', 'bao-cao-tien-quy');
		$collection->add('bangDiem', $this->getRouterIdParameter() . '/bang-diem/{hocKy}/{action}');
		$collection->add('thieuNhiPhanDoanDownloadBangDiem',  '/download-bang-diem/hoc-ky-{hocKy}');
		
		parent::configureRoutes($collection);
	}
	
	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$action       = $this->action;
		$actionParams = $this->actionParams;
		
		// this text filter will be used to retrieve autocomplete fields
		$datagridMapper
			->add('id', null, [ 'label' => 'list.label_id' ]);
	}
	
	/**
	 * @param string  $name
	 * @param ChiDoan $object
	 *
	 * @return bool|mixed
	 */
	public function isGranted($name, $object = null) {
		$container = $this->getConfigurationPool()->getContainer();
		
		if($name === 'DELETE') {
			return false;
		}
		
		$tv = $this->getUserThanhVien();
		
		if(empty($tv) || ! $tv->isEnabled()) {
			return false;
		}
		
		if( ! $tv->isPhanDoanTruongOrSoeur()) {
			return false;
		}
		
		return true;


//		return parent::isGranted($name, $object);
	}
	
	public function createQuery($context = 'list') {
		/** @var ProxyQuery $query */
		$query = parent::createQuery($context);
		/** @var QueryBuilder $qb */
		$qb        = $query->getQueryBuilder();
		$expr      = $qb->expr();
		$rootAlias = $qb->getRootAliases()[0];
		
		$tv       = $this->getUserThanhVien();
		$phanDoan = $tv->getPhanDoan();
		
		$query->andWhere($expr->eq($rootAlias . '.phanDoan', $expr->literal($phanDoan)));
		$query->andWhere($expr->eq($rootAlias . '.namHoc', $expr->literal($tv->getNamHoc())));
		
		return $query;
	}
	
	public function generateUrl($name, array $parameters = array(), $absolute = UrlGeneratorInterface::ABSOLUTE_PATH) {
		if($this->action === 'list-thieu-nhi') {
			if($name === 'list') {
				$name = 'thieuNhi';
			}
		} elseif($this->action === 'duyet-bang-diem') {
			if($name === 'list') {
				$parameters['action'] = 'duyet-bang-diem';
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
		
		if(empty($this->action)) {
			$request      = $this->getRequest();
			$this->action = $request->query->get('action', '');
		}
		
		$listMapper
//			->addIdentifier('id')
			->add('id', null, array());
		if($this->action === 'bao-cao-tien-quy') {
			$listMapper->add('_progress', null, array(
				'header_style' => 'width: 30%; text-align: center',
				
				'label'    => 'list.label_progress'
			,
				'template' => 'admin/ban-quan-tri/chi-doan/list-bao-cao-tien-quy__field__progress.html.twig'
			));
			$listMapper->add('_so_thieu_nhi', null, array(
				'label'    => 'list.label_so_thieu_nhi'
			,
				'template' => 'admin/ban-quan-tri/chi-doan/list-bao-cao-tien-quy__field__so_thieu_nhi.html.twig'
			));
			$listMapper->add('_so_tien', null, array(
				'label'    => 'list.label_so_tien'
			,
				'template' => 'admin/ban-quan-tri/chi-doan/list-bao-cao-tien-quy__field__so_tien.html.twig'
			));
			$listMapper->add('_so_thieu_nhi_ngheo', null, array(
				'label'    => 'list.label_so_thieu_nhi_ngheo'
			,
				'template' => 'admin/ban-quan-tri/chi-doan/list-bao-cao-tien-quy__field__so_thieu_nhi_ngheo.html.twig'
			));
		} elseif($this->action === 'duyet-bang-diem') {
			$listMapper->add('_action', 'actions', array(
				'actions' => array(
					'duyet_bang_diem' => array( 'template' => 'admin/phan-doan-truong/chi-doan/list__action__duyet_bang_diem.html.twig' ),
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
					'TB Miệng HK-2'  => 'quizTerm2'
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