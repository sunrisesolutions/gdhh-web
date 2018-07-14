<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class ThieuNhiAdminController extends BaseCRUDAdminController {
	
	
	public function sanhHoatLaiAction($id = null, Request $request) {
		/**
		 * @var ThanhVien $thanhVien
		 */
		$thanhVien = $this->admin->getSubject();
		if( ! $thanhVien) {
			throw new NotFoundHttpException(sprintf('unable to find the Thieu-nhi with id : %s', $id));
		}
		
		/** @var ThieuNhiAdmin $admin */
		$admin         = $this->admin;
		$namHocService = $this->get('app.binhle_thieunhi_namhoc');
		$phanBo        = $thanhVien->sanhHoatLai($namHocService->getNamHocHienTai());
		
		$manager = $this->get('doctrine.orm.default_entity_manager');
		$manager->persist($phanBo);
		$manager->persist($thanhVien);
		try {
			$manager->flush();
		} catch(Exception $e) {
			$this->addFlash('sonata_flash_error', $e);
		}
		
		$this->addFlash('sonata_flash_success', $thanhVien->getName() . ' đã tham gia trở lại.');
		
		$params      = $this->getRefererParams();
		$routeParams = $params;
		unset($routeParams['_route']);
		unset($routeParams['_controller']);
		unset($routeParams['_sonata_admin']);
		unset($routeParams['_sonata_name']);
		unset($routeParams['_locale']);
		
		
		return $this->redirect($this->generateUrl(
			$params['_route'],
			$routeParams
		));
	}
	
	public function nghiSanhHoatAction($id = null, Request $request) {
		/**
		 * @var ThanhVien $thanhVien
		 */
		$thanhVien = $this->admin->getSubject();
		if( ! $thanhVien) {
			throw new NotFoundHttpException(sprintf('unable to find the Thieu-nhi with id : %s', $id));
		}
		
		/** @var ThieuNhiAdmin $admin */
		$admin         = $this->admin;
		$namHocService = $this->get('app.binhle_thieunhi_namhoc');
		$thanhVien->setEnabled(false);
		
		$manager = $this->get('doctrine.orm.default_entity_manager');
		$manager->persist($thanhVien);
		try {
			$manager->flush();
		} catch(Exception $e) {
			$this->addFlash('sonata_flash_error', $e);
		}
		
		$this->addFlash('sonata_flash_success', $thanhVien->getName() . ' đã nghỉ sanh hoạt.');
		
		$params = $this->getRefererParams();
		if(empty($params)) {
			return $this->redirect($this->generateUrl('admin_app_binhle_thieunhi_thieunhi_list'
			));
		}
		$routeParams = $params;
		unset($routeParams['_route']);
		unset($routeParams['_controller']);
		unset($routeParams['_sonata_admin']);
		unset($routeParams['_sonata_name']);
		unset($routeParams['_locale']);
		
		
		return $this->redirect($this->generateUrl(
			$params['_route'],
			$routeParams
		));
	}
	
	public function thieuNhiNhomAction(PhanBo $phanBo, Request $request) {
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		
		$cacDoiNhomGiaoLy = $phanBo->getCacDoiNhomGiaoLyPhuTrach();
		
		$admin->setAction('list-thieu-nhi-nhom');
		$admin->setActionParams([
			'phanBo'           => $phanBo,
			'cacDoiNhomGiaoLy' => $cacDoiNhomGiaoLy,
			'chiDoan'          => $phanBo->getChiDoan()
		]);
		
		return parent::listAction();
	}
	
	public function thieuNhiChiDoanAction(PhanBo $phanBo, Request $request) {
		
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		
		$admin->setAction('list-thieu-nhi-chi-doan');
		$admin->setActionParams([
			'phanBo'  => $phanBo,
			'chiDoan' => $phanBo->getChiDoan()
		]);
		
		return parent::listAction();
	}
	
	public function thieuNhiPhanDoanAction($phanDoan, Request $request) {
		$actionParams                    = [ 'phanDoan' => $phanDoan ];
		$phanDoan                        = strtoupper($phanDoan);
		$cd                              = ThanhVien::getDanhSachChiDoanTheoPhanDoan($phanDoan);
		$actionParams['danhSachChiDoan'] = $cd;
		
		/** @var ThieuNhiAdmin $admin */
		$admin = $this->admin;
		$admin->setAction('list-thieu-nhi-phan-doan');
		$admin->setActionParams($actionParams);
		
		if( ! empty($namHoc = $this->get(NamHocService::class)->getNamHocHienTai())) {
			$admin->setNamHoc($namHoc->getId());
		}
		
		return parent::listAction();
		
		
	}
}