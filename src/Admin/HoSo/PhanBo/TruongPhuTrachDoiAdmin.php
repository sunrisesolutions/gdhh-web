<?php

namespace App\Admin\HoSo\PhanBo;

use App\Admin\BaseAdmin;
use App\Entity\HocBa\HienDien;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Service\HoSo\NamHocService;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as RoutingUrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class TruongPhuTrachDoiAdmin extends BaseAdmin
{

    const ENTITY = PhanBo::class;

    protected $baseRouteName = 'admin_app_hoso_phanbo_truongphutrachdoi';

    protected $baseRoutePattern = '/app/hoso-phanbo/truongphutrachdoi';
    protected $datagridValues = [
        '_page' => 1,
        // reverse order (default = 'DESC')
        '_sort_order' => 'ASC',

        // name of the ordered field (default = the model's id field, if any)
        '_sort_by' => 'thanhVien.firstname',
    ];

    protected $maxPerPage = 256;

    /** @var  NamHoc $namHoc */
    public $namHoc;

    public function getTargetDates($type = 'ALL')
    {
        /** @var PhanBo $phanBoTruong */
        $phanBoTruong = $this->getSubject();

        $schoolYear = $this->getConfigurationPool()->getContainer()->get(NamHocService::class)->getNamHocHienTai()->getId();
        $schoolYear = (int)$schoolYear;
        $schoolYearDate = new \DateTime();
        $schoolYearDate->setDate($schoolYear, 9, 6);
        $nextYearDate = new \DateTime();
        $nextYearDate->setDate($schoolYear + 1, 6, 1);
        $today = new \DateTime();
        $fourWeeksAgo = new \DateTime();
        $fourWeeksAgo->modify('-4 weeks');

        $qb = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.default_entity_manager')->createQueryBuilder();
        $qb->select('dcc')->from(DiemChuyenCan::class, 'dcc');
        $qb->where('dcc.targetDate BETWEEN :fourWeeksAgo AND :today')
            ->setParameter('fourWeeksAgo', $fourWeeksAgo->format('Y-m-d'))
            ->setParameter('today', $today->format('Y-m-d'))
            ->orderBy('dcc.targetDate', 'DESC');;

//$qb->where('e.fecha > :monday')
//   ->andWhere('e.fecha < :sunday')
//   ->setParameter('monday', $monday->format('Y-m-d'))
//   ->setParameter('sunday', $sunday->format('Y-m-d'));

        $result = $qb->getQuery()->getResult();
        if ($type === 'ALL') {
            return $result;
        } elseif ($type === 'T5') {
            $dccs = [];
            /** @var DiemChuyenCan $dcc */
            foreach ($result as $dcc) {
                if (strtoupper($dcc->getTargetDate()->format('l')) == 'THURSDAY') {
                    $dccs[] = $dcc;
                }
            }

            return $dccs;
        } elseif ($type === 'CN') {
            $dccs = [];
            /** @var DiemChuyenCan $dcc */
            foreach ($result as $dcc) {
                if (strtoupper($dcc->getTargetDate()->format('l')) == 'SUNDAY') {
                    $dccs[] = $dcc;
                }
            }

            return $dccs;
        }

        /** @var DiemChuyenCan $dcc */
//		foreach($result as $dcc) {
//			$x = $dcc;
//			$output->writeln([ 'dcc', $dcc->getTargetDate()->format('Y-m-d') ]);
//			if($dow = strtoupper($dcc->getTargetDate()->format('l')) == 'THURSDAY') {
//				$output->writeln('this is a thursday');
//			};
    }

    public function getTemplate($name)
    {
        if ($name === 'list') {
            if ($this->action === 'dong-quy') {
                return 'admin/truong-phu-trach-doi/list-dong-quy-thieu-nhi.html.twig';
            }
            if ($this->action === 'nhap-diem-thieu-nhi' || $this->action === 'nop-bang-diem') {
                return 'admin/truong-phu-trach-doi/list-nhap-diem-thieu-nhi.html.twig';
            }
        }

        return parent::getTemplate($name);
    }

    public function configureRoutes(RouteCollection $collection)
    {
        parent::configureRoutes($collection);
        $collection->add('dongQuy', $this->getRouterIdParameter() . '/dong-quy');
        $collection->add('nhapDiemThieuNhi', $this->getRouterIdParameter() . '/nhap-diem-thieu-nhi');
        $collection->add('thieuNhiNhomDownloadBangDiem', $this->getRouterIdParameter() . '/bang-diem/hoc-ky-{hocKy}/download');
        $collection->add('nopBangDiem', $this->getRouterIdParameter() . '/nop-bang-diem/{hocKy}');
        $collection->add('diemDanhThu5', $this->getRouterIdParameter() . '/diem-danh-thu-5');
        $collection->add('diemDanhChuaNhat', $this->getRouterIdParameter() . '/diem-danh-chua-nhat');
        $collection->add('tinhDiemChuyenCan', $this->getRouterIdParameter() . '/tinh-diem-chuyen-can/{hocKy}');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        // this text filter will be used to retrieve autocomplete fields
        $datagridMapper
            ->add('thanhVien.id', null, [
                'label' => 'list.label_id',
                'show_filter' => true
            ])
            ->add('thanhVien.name', null, [
                'label' => 'list.label_name',
                'show_filter' => true
            ])
            ->add('thanhVien.chiDoan', null, [
                'label' => 'list.label_chi_doan',
                'show_filter' => true
            ])
            ->add('doiNhomGiaoLy.number', null, [
                'label' => 'list.label__nhom_giao_ly',
                'show_filter' => true
            ]);
    }

    /**
     * @param string $name
     * @param PhanBo $object
     *
     * @return bool|mixed
     */
    public function isGranted($name, $object = null)
    {

        $tv = $this->getUserThanhVien();
        if (empty($tv) || !$tv->isEnabled()) {
            return false;
        }

        if ($name === 'NOP_BANG_DIEM') {
            if (empty($object) || empty($hocKy = $this->actionParams['hocKy'])) {
                return false;
            }

            return $object->coTheNopBangDiem($hocKy);
        }

        if ($name === 'LIST') {
            return true;
        }

        return false;
    }

    public function createQuery($context = 'list')
    {
        /** @var ProxyQuery $query */
        $query = parent::createQuery($context);
        /** @var QueryBuilder $qb */
        $qb = $query->getQueryBuilder();
        $expr = $qb->expr();
        $rootAlias = $qb->getRootAliases()[0];
        $qb->join($rootAlias . '.chiDoan', 'chiDoan');
        $qb->join($rootAlias . '.namHoc', 'namHoc');
        $qb->join($rootAlias . '.thanhVien', 'thanhVien');
        $qb->andWhere($expr->eq('thanhVien.enabled', $expr->literal(true)));

        /** @var PhanBo $phanBoTruong */
        $phanBoTruong = $this->getSubject();

        if (!empty($chiDoan = $phanBoTruong->getChiDoan())) {
            if ($phanBoTruong->getPhanDoan() === ThanhVien::PHAN_DOAN_THIEU) {
                if ($this->action === 'diem-danh-t5') {
                    $qb->andWhere($expr->in('chiDoan.number', [10, 11, 12]));
                    $qb->andWhere($expr->eq('namHoc.id', $this->getConfigurationPool()->getContainer()->get(NamHocService::class)->getNamHocHienTai()->getId()));
                }
            } else {
                $qb->andWhere($expr->like('chiDoan.id', $expr->literal($chiDoan->getId())));
            }
        }

        $phanDoan = $phanBoTruong->getPhanDoan();

        $qb->andWhere($expr->like($rootAlias . '.phanDoan', $expr->literal($phanDoan)));
        $qb->andWhere($expr->eq('namHoc.id', $phanBoTruong->getNamHoc()->getId()));

//		if($this->action === 'diem-danh-t5') {
        $qb->andWhere($expr->eq($rootAlias . '.thieuNhi', $expr->literal(true)));
//		}

        if ($this->action === 'diem-danh-cn') {
            if ($phanDoan !== ThanhVien::PHAN_DOAN_NGHIA && $phanDoan !== ThanhVien::PHAN_DOAN_TONG_DO) {
                $dnglPhuTrach = $phanBoTruong->getCacDoiNhomGiaoLyPhuTrach();
                $dnglIds = [];
                /** @var DoiNhomGiaoLy $dngl_phu_trach */
                foreach ($dnglPhuTrach as $dngl_phu_trach) {
                    $dnglIds[] = $dngl_phu_trach->getId();
                }
                if ($phanBoTruong->getThanhVien()->isCDTorGreater()) {
                    $qb->andWhere($expr->like('chiDoan.id', $expr->literal($chiDoan->getId())));
                } elseif (count($dnglIds) > 0) {
                    $qb->join($rootAlias . '.doiNhomGiaoLy', 'dngl');
                    $qb->andWhere($expr->in('dngl.id', $dnglIds));
                } elseif (!$phanBoTruong->getThanhVien()->isCDTorGreater()) {
                    $this->clearResults($query);
                }
            }
        }
        $sql = $qb->getQuery()->getSQL();

        return $query;
    }

    public function generateUrl($name, array $parameters = [], $absolute = RoutingUrlGeneratorInterface::ABSOLUTE_PATH)
    {
        if ($name === 'list') {
            if ($this->action === 'diem-danh-t5') {
//				$parameters = array_merge($parameters, [ 'action' => $this->action ]);
            }
        }

        return parent::generateUrl($name, $parameters, $absolute);
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('thanhVien.id', null, array('label' => 'list.label_id'))
            ->add('doiNhomGiaoLy.tenCacTruongPhuTrach', null, array(
                'label' => 'list.label__nhom_giao_ly',
                '_sort_order' => 'ASC',
                'sort_parent_association_mappings' => [['fieldName' => 'doiNhomGiaoLy']],
                'sort_field_mapping' => ['fieldName' => 'id'],
                'sortable' => true,
            ))
            ->add('thanhVien.lastName', null, array(
                'label' => 'list.label_lastname',
                '_sort_order' => 'ASC',
                'sort_parent_association_mappings' => [['fieldName' => 'thanhVien']],
                'sort_field_mapping' => ['fieldName' => 'firstname'],
                'sortable' => true,
            ))
            ->add('thanhVien.middleName', null, array(
                'label' => 'list.label_middlename',
                '_sort_order' => 'ASC',
                'sort_parent_association_mappings' => [['fieldName' => 'thanhVien']],
                'sort_field_mapping' => ['fieldName' => 'firstname'],
                'sortable' => true,
            ))
            ->add('thanhVien.firstName', null, array(
                'label' => 'list.label_firstname',
                '_sort_order' => 'ASC',
                'sort_parent_association_mappings' => [['fieldName' => 'thanhVien']],
                'sort_field_mapping' => ['fieldName' => 'firstname'],
                'sortable' => true,
            ))
//			->add('_action', 'actions', array(
//				'actions' => array(
//					'edit'   => array(),
//					'delete' => array(),
////					'send_evoucher' => array( 'template' => '::admin/employer/employee/list__action_send_evoucher.html.twig' )
//
////                ,
////                    'view_description' => array('template' => '::admin/product/description.html.twig')
////                ,
////                    'view_tos' => array('template' => '::admin/product/tos.html.twig')
//				)
//			))
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $isAdmin = $this->isAdmin();
        $container = $this->getConfigurationPool()->getContainer();

        $formMapper
            ->tab('form.tab_info')
            ->with('form.group_general')//            ->add('children')
            ->add('id', null, array('label' => 'list.label_nam_hoc'));


        $formMapper
            ->end()
            ->end();

    }

    /**
     * @param NamHoc $object
     */
    public function preValidate($object)
    {

    }

    /** @param NamHoc $object */
    public function prePersist($object)
    {

    }
}
