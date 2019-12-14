<?php

namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HocBa\HienDien;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use App\Service\HoSo\NamHocService;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
	
	public function diemDanhAction($id = null, $truongId, $dtStr, $type, $action = 'present', Request $request) {
		/**
		 * @var ThanhVien $thieuNhi
		 */
		$thieuNhi = $this->admin->getSubject();
		
		if( ! $thieuNhi) {
			throw new NotFoundHttpException(sprintf('unable to find the Thieu-nhi with id: %s', $id));
		}
		
		if( ! $thieuNhi->isThieuNhi()) {
			throw new NotFoundHttpException(sprintf('This is is not a kid: %s', $id));
		}
		
		if( ! in_array($type, [ HienDien::TYPE_DIEM_DANH_NONG, HienDien::TYPE_GIAO_LY, HienDien::TYPE_LE_CN ])) {
			throw new NotFoundHttpException(sprintf('Invalid Type: %s', $type));
		}
		
		$truong         = $this->getDoctrine()->getRepository(ThanhVien::
		class)->find($truongId);
		$phanBoTruongId = $truong->getPhanBoNamNay();
		/** @var PhanBo $phanBoTruong */
		$phanBoTruong = $this->getDoctrine()->getRepository(PhanBo::
		class)->find($phanBoTruongId);
		
		
		/** @var ThieuNhiAdmin $admin */
		$admin         = $this->admin;
		$namHocService = $this->get(NamHocService::class);
		$manager       = $this->getDoctrine()->getManager();
		
		$phanBoTN = $thieuNhi->getPhanBoNamNay();
		$phanBoTN->initiateDiemDanhCache();
		
		$targetDate = \DateTime::createFromFormat('Y-m-d H:i:s', $dtStr);
		
		$hienDien = $phanBoTN->getHienDienByTargetDateType($targetDate, $type);
		
		if(empty($hienDien)) {
			$status = 'absent';
		} else {
			$status = 'present';
		}
		
		if($action === 'present') {
			$dcc = $this->getDoctrine()->getRepository(DiemChuyenCan::class)->findOneBy(['targetDate' => $targetDate]);
			$hienDien = $phanBoTruong->diemDanh($dcc, $phanBoTN, $targetDate, $type);
			$manager->persist($hienDien);
			$manager->flush($hienDien);
			
			return new JsonResponse([ 'OK' ]);
		} elseif($action === 'absent') {
			if( ! empty($hienDien)) {
				$manager->remove($hienDien);
				$manager->flush($hienDien);
			}
			
			return new JsonResponse([ 'OK' ]);
		} elseif($action === 'status') {
			return new JsonResponse([ 'status' => $status ]);
		}
	}
	
}
