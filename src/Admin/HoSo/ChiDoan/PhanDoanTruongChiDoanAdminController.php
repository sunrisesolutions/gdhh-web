<?php

namespace App\Admin\HoSo\ChiDoan;

use App\Admin\BaseCRUDAdminController;
use App\Admin\HoSo\ChiDOan\PhanDoanTruongChiDoanAdmin;

use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\NamHoc;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhanDoanTruongChiDoanAdminController extends BaseCRUDAdminController {
	
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
			throw new InvalidArgumentException();
		}
		
		if( ! in_array(intval($hocKy), [ 1, 2 ])) {
			throw new InvalidArgumentException();
		}
		
		$manager = $this->get('doctrine.orm.entity_manager');
		
		$setterDuyetBandDiemMethod   = 'setDuocDuyetBangDiemHK' . $hocKy;
		$setterHoanTatBandDiemMethod = 'setHoanTatBangDiemHK' . $hocKy;
		
		if($action === 'duyet') {
			$chiDoan->$setterDuyetBandDiemMethod(true);
		} elseif($action === 'tra-ve') {
			$chiDoan->$setterDuyetBandDiemMethod(false);
			$chiDoan->$setterHoanTatBandDiemMethod(false);
			/** @var DoiNhomGiaoLy $dngl */
			foreach($chiDoan->getCacDoiNhomGiaoLy() as $dngl) {
				$dnglSetterMethod = 'setDuyetBangDiemHK' . $hocKy . 'CDT';
				$dngl->$dnglSetterMethod(false);
				$manager->persist($dngl);
			}
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

//		return new RedirectResponse($this->generateUrl('admin_app_binhle_thieunhi_phandoantruong_chidoan_list', [ 'action' => 'duyet-bang-diem' ]));
		$params = $this->getRefererParams();
		
		if( ! empty($params)) {
			$routeParams = $params;
			unset($routeParams['_route']);
			unset($routeParams['_controller']);
			unset($routeParams['_sonata_admin']);
			unset($routeParams['_sonata_name']);
			unset($routeParams['_locale']);
			$route                 = $params['_route'];
			$routeParams['action'] = 'duyet-bang-diem';
		} else {
			$route                 = 'admin_app_hoso_chidoan_phandoantruong_chidoan_list';
			$routeParams           = [];
			$routeParams['action'] = 'duyet-bang-diem';
		}
		
		return $this->redirect($this->generateUrl(
			$route,
			$routeParams
		));
	}
	
	public function thieuNhiPhanDoanDownloadBangDiemAction($hocKy, Request $request) {
		if( ! in_array($hocKy, [ 1, 2 ])) {
			throw new \InvalidArgumentException();
		}
		
		/** @var PhanDoanTruongChiDoanAdmin $admin */
		$admin = $this->admin;
		
		//		\PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
		$hocKy = intval($hocKy);
		
		/** @var ThanhVien $thanhVien */
		$thanhVien = $this->getUser()->getThanhVien();
		
		$pdt = $thanhVien->getPhanDoanTruongObj();
		
		$response = $pdt->downloadBangDiemExcel($hocKy);
		
		$response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
		$response->headers->set('Pragma', 'public');
		$response->headers->set('Cache-Control', 'maxage=1');
		
		$filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
		$response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
		
		return $response;
	}
	
}