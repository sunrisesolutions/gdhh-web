<?php
namespace App\Admin\HoSo\ChiDoan;

use App\Admin\BaseAdmin;
use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\ThanhVien;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

class BanQuanTriChiDoanAdminController extends BaseCRUDAdminController {
	
	public function baoCaoTienQuyAction(Request $request) {
		/** @var PhanDoanTruongChiDoanAdmin $admin */
		$admin = $this->admin;

//		if( ! empty($namHoc = $this->get('app.binhle_thieunhi_namhoc')->getNamHocHienTai())) {
//			$admin->setNamHoc($namHoc->getId());
//		}
		
		$admin->setAction('bao-cao-tien-quy');
		
		return parent::listAction();
		
	}
	
	public function bangDiemAction($id = null, $hocKy = null, $action = null, Request $request) {
		/**
		 * @var ChiDoan $chiDoan
		 */
		$chiDoan = $this->admin->getSubject();
		if( ! $chiDoan) {
			throw new NotFoundHttpException(sprintf('Unable to find the Chi Doan with id : %s', $id));
		}
		
		/** @var PhanDoanTruongChiDoanAdmin $admin */
		$admin = $this->admin;
		
		if( ! in_array($action, [ 'duyet', 'tra-ve' ])) {
			throw new \InvalidArgumentException();
		}
		
		if( ! in_array(intval($hocKy), [ 1, 2 ])) {
			throw new \InvalidArgumentException();
		}
		
		$manager = $this->get('doctrine.orm.entity_manager');
		
		$setterDuyetBandDiemMethod   = 'setDuocDuyetBangDiemHK' . $hocKy . 'CDT';
		$setterHoanTatBandDiemMethod = 'setHoanTatBangDiemHK' . $hocKy;
		
		if($action === 'duyet') {
			$chiDoan->$setterDuyetBandDiemMethod(true);
		} elseif($action === 'tra-ve') {
			$chiDoan->$setterDuyetBandDiemMethod(false);
			$chiDoan->$setterHoanTatBandDiemMethod(false);
		}
		
		try {
			$manager->persist($chiDoan);
			$manager->flush();
			if($action === 'duyet') {
				$this->addFlash('sonata_flash_success', sprintf("Bảng điểm Chi-đoàn %s đã được duyệt.", $chiDoan->getNumber()));
			} elseif($action === 'tra-ve') {
				$this->addFlash('sonata_flash_success', sprintf("Bảng điểm Chi-đoàn %s đã bị trả về.", $chiDoan->getNumber()));
			}
			
		} catch(\Exception $exception) {
			$this->addFlash('sonata_flash_error', $exception->getMessage());
		}
		
		return new RedirectResponse($this->generateUrl('admin_app_hoso_chidoan_banquantri_chidoan_list', ['action'=>'duyet-bang-diem']));
	}
	
}