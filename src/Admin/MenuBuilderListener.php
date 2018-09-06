<?php

namespace App\Admin;

use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use App\Service\User\UserService;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MenuBuilderListener {
	/**
	 * @var ContainerInterface
	 */
	private $container;
	
	/**
	 * @var ItemInterface
	 */
	private $menu;
	
	/**
	 * @var ItemInterface
	 */
	private $dauNam;
	
	/**
	 * @var ItemInterface
	 */
	private $banQuanTri;
	
	/**
	 * @var ItemInterface
	 */
	private $diemGiaoLy;
	
	/**
	 * @var ItemInterface
	 */
	private $thieuNhi;
	
	/**
	 * @var ItemInterface
	 */
	private $huynhTruong;
	
	/**
	 * @var UserService $userService
	 */
	private $userService;
	
	function __construct(UserService $userService, ContainerInterface $c) {
		$this->container   = $c;
		$this->userService = $userService;
	}
	
	public function addMenuItems(ConfigureMenuEvent $event) {
		$user    = $this->userService->getUser();
		$request = $this->container->get('request_stack')->getCurrentRequest();
		if($user->isAdmin()) {
			$event->getMenu()->addChild('list ten thanh', array(
				'route'           => 'admin_app_hoso_christianname_list',
				'routeParameters' => [],
				'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
			))->setLabel($this->container->get('translator')->trans('dashboard.binhle_thieunhi_tenthanh', [], 'BinhLeAdmin'));
			
			return;
		}
		
		if( ! empty($thanhVien = $user->getThanhVien()) && $thanhVien->isEnabled()) {
			if($user->hasRole(User::ROLE_HUYNH_TRUONG)) {
				$menu       = $event->getMenu();
				$this->menu = $menu;
				$translator = $this->container->get('translator');
				
				$menu->setChildren([]);
				
				$this->dauNam     = $menu->addChild('thieunhi_daunam')->setLabel($translator->trans('dashboard.thieunhi_daunam', [], 'BinhLeAdmin'));
				$this->diemGiaoLy = $menu->addChild('thieunhi_diemgiaoly')->setLabel($translator->trans('dashboard.thieunhi_diemgiaoly', [], 'BinhLeAdmin'));
				$this->thieuNhi   = $menu->addChild('danh sach thieu nhi')->setLabel($translator->trans('dashboard.danh_sach_thieu_nhi', [], 'BinhLeAdmin'));
				
				$this->huynhTruong = $menu->addChild('danh sach huynh truong')->setLabel($translator->trans('dashboard.danh_sach_truong', [], 'BinhLeAdmin'));
				
				
				$this->huynhTruong->addChild('Danh sach Huynh Truong', array(
					'route'           => 'admin_app_hoso_thanhvien_huynhtruong_list',
					'routeParameters' => [],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.huynhtruong_xu_doan', [], 'BinhLeAdmin'));
				
				$this->addThanhVienMenuItems($translator, $thanhVien);
				$baoCaoTienQuy = false;
				
				if($thanhVien->isBQT()) {
//					$this->banQuanTri = $menu->addChild('thieunhi_banquantri')->setLabel($translator->trans('dashboard.thieunhi_banquantri', [], 'BinhLeAdmin'));
//					$this->addBanQuanTriMenuItems($translator, $thanhVien, []);
					
					$this->diemGiaoLy->addChild('chi doan (duyet diem)', array(
						'route'           => 'admin_app_hoso_chidoan_banquantri_chidoan_list',
						'routeParameters' => [ 'action' => 'duyet-bang-diem' ],
						'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
					))->setLabel($translator->trans('dashboard.thieunhi_duyet_diem', [], 'BinhLeAdmin'));
					
					$baoCaoTienQuy = true;
				} elseif($thanhVien->isPhanDoanTruongOrSoeur()) {
//					$this->banQuanTri = $menu->addChild('thieunhi_banquantri')->setLabel($translator->trans('dashboard.thieunhi_phandoantruong', [], 'BinhLeAdmin'));
//					$this->addBanQuanTriMenuItems($translator, $thanhVien, []);
				}
				
				if($thanhVien->isThuQuyXuDoan() || $baoCaoTienQuy) {
					$this->dauNam->addChild('BQT va Thu Quy duyet tien quy', array(
						'route'           => 'admin_app_hoso_chidoan_banquantri_chidoan_baoCaoTienQuy',
						'routeParameters' => [],
						'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
					))->setLabel($translator->trans('dashboard.thieunhi_bao_cao_tien_quy', [], 'BinhLeAdmin'));
				}
				
			}
		}
	}
	
	private function addThanhVienMenuItems($translator, ThanhVien $thanhVien, $params = array()) {
		$phanBo = $thanhVien->getPhanBoNamNay();
		
		if( ! empty($phanBo)) {
			$chiDoan = $phanBo->getChiDoan();
			
			if( ! empty($phanBo->getPhanDoan())) {
				$this->huynhTruong->addChild('list truong phan doan', array(
					'route'           => 'admin_app_hoso_thanhvien_huynhtruong_truongPhanDoan',
					'routeParameters' => [ 'phanDoan' => strtolower($phanBo->getPhanDoan()) ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.truong_phandoan', [], 'BinhLeAdmin'));
			}
			
			if($phanBo->getCacTruongPhuTrachDoi()->count() > 0) {
				$this->diemGiaoLy->addChild('diem danh thu 5', array(
					'route'           => 'admin_app_hoso_phanbo_truongphutrachdoi_diemDanhThu5',
					'routeParameters' => [ 'id' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_diemdanh_t5', [], 'BinhLeAdmin'));
				
				$this->diemGiaoLy->addChild('nhap bang diem cho nhom minh', array(
					'route'           => 'admin_app_hoso_phanbo_truongphutrachdoi_nhapDiemThieuNhi',
					'routeParameters' => [ 'id' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_nhapdiem_nhomphutrach', [], 'BinhLeAdmin'));
				
				$cacTruong = $phanBo->getCacTruongPhuTrachDoi();
				$numberStr = '';
				/** @var TruongPhuTrachDoi $truong */
				foreach($cacTruong as $truong) {
					$numberStr .= $truong->getDoiNhomGiaoLy()->getNumber() . ' ';
				}
				if( ! empty($numberStr)) {
					$numberStr = ' ( ' . $numberStr . ' )';
				}
				$this->thieuNhi->addChild('thieu nhi trong nhom minh', array(
					'route'           => 'admin_app_hoso_thanhvien_thieunhi_thieuNhiNhom',
					'routeParameters' => [ 'phanBo' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_nhomphutrach', [], 'BinhLeAdmin') . $numberStr);
			}
			
			if($phanBo->isThuKyChiDoan()) {
				$this->diemGiaoLy->addChild('nhap bang diem cho chi doan minh', array(
					'route'           => 'admin_app_hoso_phanbo_thukychidoan_nhapDiemThieuNhi',
					'routeParameters' => [ 'id' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thukychidoan_nhapdiem', [], 'BinhLeAdmin'));
			}
			
			if($phanBo->isChiDoanTruong()) {
				$this->dauNam->addChild('chia doi trong chi doan', array(
					'route'           => 'admin_app_hoso_chidoan_chidoantruong_chidoan_thieuNhiChiDoanChiaDoi',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chia_doi_chi_doan', [], 'BinhLeAdmin'));
				
				$this->dauNam->addChild('chia doi truong chi doan', array(
					'route'           => 'admin_app_hoso_chidoan_chidoantruong_chidoan_thieuNhiChiDoanChiaTruongPhuTrach',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chia_truong_chi_doan', [], 'BinhLeAdmin'));
				
				$this->dauNam->addChild('bao cao tien quy cho Chi Doan Truong duyet tien quy', array(
					'route'           => 'admin_app_hoso_doinhomgiaoly_truongphutrach_dngl_baoCaoTienQuy',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_bao_cao_tien_quy', [], 'BinhLeAdmin'));
				
				$this->diemGiaoLy->addChild('doi nhom giao ly (duyet diem)', array(
					'route'           => 'admin_app_hoso_doinhomgiaoly_truongphutrach_dngl_list',
					'routeParameters' => [],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_duyet_diem', [], 'BinhLeAdmin'));
				
			}
			
			if( ! empty($this->dauNam)) {
				if($phanBo->getCacTruongPhuTrachDoi()->count() > 0) {
					$this->dauNam->addChild('truong phu trach ghi nhan tien quy', array(
						'route'           => 'admin_app_hoso_phanbo_truongphutrachdoi_dongQuy',
						'routeParameters' => [ 'id' => $phanBo->getId() ],
						'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
					))->setLabel($translator->trans('dashboard.thieunhi_dong_quy', [], 'BinhLeAdmin'));
				}
			}
			
			if( ! empty($chiDoan)) {
				$this->thieuNhi->addChild('thieu nhi trong Chi-doan minh', array(
					'route'           => 'admin_app_hoso_thanhvien_thieunhi_thieuNhiChiDoan',
					'routeParameters' => [ 'phanBo' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chidoanphutrach', [], 'BinhLeAdmin'));
				
				$this->huynhTruong->addChild('truong chi doan', array(
					'route'           => 'admin_app_hoso_thanhvien_huynhtruong_truongChiDoan',
					'routeParameters' => [ 'chiDoan' => $chiDoan->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.truong_chidoan', [], 'BinhLeAdmin'));
			}
			
			if($phanBo->isPhanDoanTruongOrSoeur()) {
				$this->dauNam->addChild('phan doan truong duyet tien quy', array(
					'route'           => 'admin_app_hoso_chidoan_phandoantruong_chidoan_baoCaoTienQuy',
					'routeParameters' => [],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_bao_cao_tien_quy', [], 'BinhLeAdmin'));
				
				$this->diemGiaoLy->addChild('chi doan (duyet diem)', array(
					'route'           => 'admin_app_hoso_chidoan_phandoantruong_chidoan_list',
					'routeParameters' => [ 'action' => 'duyet-bang-diem' ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_phan_doan_truong_duyet_diem', [], 'BinhLeAdmin'));
				
			}
			
			if( ! empty($phanBo->getPhanDoan())) {
				$this->thieuNhi->addChild('list thieu nhi phan doan', array(
					'route'           => 'admin_app_hoso_thanhvien_thieunhi_thieuNhiPhanDoan',
					'routeParameters' => [ 'phanDoan' => strtolower($phanBo->getPhanDoan()) ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_phandoan', [], 'BinhLeAdmin'));
			}
			
			$this->thieuNhi->addChild('list thieu nhi toan xu doan', array(
				'route'           => 'admin_app_hoso_thanhvien_thieunhi_list',
//			'routeParameters' => [ 'id' => $salesPartnerId ],
				'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
			))->setLabel($translator->trans('dashboard.list_thieunhi_xudoan', [], 'BinhLeAdmin'));
		}
	}
	
}
