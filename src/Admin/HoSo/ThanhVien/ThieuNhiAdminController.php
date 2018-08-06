<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThieuNhiAdminController extends BaseCRUDAdminController {
	public function redirectToListView() {
		/** @var User $user */
		$user            = $this->getUser();
		$thanhVienTruong = $user->getThanhVien();
		$phanBoTruong    = $thanhVienTruong->getPhanBoNamNay();
		if($thanhVienTruong->isChiDoanTruong()) {
			return $this->redirect($this->admin->generateUrl('thieuNhiChiDoan', [ 'phanBo' => $phanBoTruong->getId() ]));
		}
		if($thanhVienTruong->isPhanDoanTruong()) {
			return $this->redirect($this->admin->generateUrl('thieuNhiPhanDoan', [ 'phanDoan' => $thanhVienTruong->getPhanDoan() ]));
		}
		if($thanhVienTruong->isBanQuanTri()) {
			return $this->redirect($this->admin->generateUrl('list'));
		}
	}
	
	public function xetLenLopAction($id = null, Request $request) {
		/**
		 * @var ThanhVien $thanhVien
		 */
		$thanhVien = $this->admin->getSubject();
		if( ! $thanhVien) {
			throw new NotFoundHttpException(sprintf('unable to find the Thieu-nhi with id : %s', $id));
		}
		
		if($this->admin->isGranted('xet-len-lop', $thanhVien)) {
			$bangDiem = $thanhVien->getPhanBoNamNay()->getBangDiem();
			$bangDiem->setFreePassGranted(true);
			$bangDiem->setGradeRetentionForced(false);
			$m = $this->get('doctrine.orm.default_entity_manager');
			$m->persist($bangDiem);
			$m->flush($bangDiem);
			$this->addFlash('sonata_flash_success', $thanhVien->getName() . ' đã được xét lên lớp .');
		} else {
			$this->addFlash('sonata_flash_error', 'Bạn không đủ quyền để xét lên lớp cho ' . $thanhVien->getName() . '');
		};
		
		$response = $this->redirectToListView();
		if($response instanceof RedirectResponse) {
			return $response;
		}
	}
	
	public function xetOLaiAction($id = null, Request $request) {
		/**
		 * @var ThanhVien $thanhVien
		 */
		$thanhVien = $this->admin->getSubject();
		if( ! $thanhVien) {
			throw new NotFoundHttpException(sprintf('unable to find the Thieu-nhi with id : %s', $id));
		}
		
		if($this->admin->isGranted('xet-o-lai', $thanhVien)) {
			$bangDiem = $thanhVien->getPhanBoNamNay()->getBangDiem();
			$bangDiem->setFreePassGranted(false);
			$bangDiem->setGradeRetentionForced(true);
			$m = $this->get('doctrine.orm.default_entity_manager');
			$m->persist($bangDiem);
			$m->flush($bangDiem);
			$this->addFlash('sonata_flash_success', $thanhVien->getName() . ' đã bị ở lại lớp.');
		} else {
			$this->addFlash('sonata_flash_error', 'Bạn không đủ quyền để xét ở lại lớp cho ' . $thanhVien->getName() . '');
		};
		
		$response = $this->redirectToListView();
		if($response instanceof RedirectResponse) {
			return $response;
		}
	}
	
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
		$namHocService = $this->get(NamHocService::class);
		$phanBo        = $thanhVien->sanhHoatLai($namHocService->getNamHocHienTai());
		if($phanBo instanceof PhanBo) {
			$manager = $this->get('doctrine.orm.default_entity_manager');
			$manager->persist($phanBo);
			$manager->persist($thanhVien);
		}
		try {
			$manager->flush();
		} catch(Exception $e) {
			$this->addFlash('sonata_flash_error', $e);
		}
		
		$this->addFlash('sonata_flash_success', $thanhVien->getName() . ' đã tham gia trở lại.');
		
		$params = $this->getRefererParams();
		
		if(empty($params)) {
			$response = $this->redirectToListView();
			if($response instanceof RedirectResponse) {
				return $response;
			}
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
		$namHocService = $this->get(NamHocService::class);
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
			$response = $this->redirectToListView();
			if($response instanceof RedirectResponse) {
				return $response;
			}
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