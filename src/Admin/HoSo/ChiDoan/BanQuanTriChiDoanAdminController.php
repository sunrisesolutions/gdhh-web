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
	
	public function thieuNhiXuDoanDownloadBangDiemAction($hocKy, Request $request) {
		if( ! in_array($hocKy, [ 1, 2 ])) {
			throw new \InvalidArgumentException();
		}
		
		/** @var BanQuanTriChiDoanAdmin $admin */
		$admin = $this->admin;
		
		//		\PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		$hocKy = intval($hocKy);
		
		/** @var ThanhVien $thanhVien */
		$thanhVien = $this->getUser()->getThanhVien();
		$bqt = $thanhVien->getBanQuanTriObj();
		
		$response = $bqt->downloadBangDiemExcel($hocKy);
		
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		
		$filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
		
		return $response;
	}
}