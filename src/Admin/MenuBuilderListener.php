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
//        $pos = $user->getPosition(['roles' => [Position::ROLE_ADMIN]]);
		
		if( ! empty($thanhVien = $user->getThanhVien()) && $thanhVien->isEnabled()) {
			if($user->hasRole(User::ROLE_HUYNH_TRUONG)) {
				$menu       = $event->getMenu();
				$this->menu = $menu;
				$translator = $this->container->get('translator');
				
				$menu->setChildren([]);
				
				$this->dauNam     = $menu->addChild('thieunhi_daunam')->setLabel($translator->trans('dashboard.thieunhi_daunam', [], 'BinhLeAdmin'));
				$this->diemGiaoLy = $menu->addChild('thieunhi_diemgiaoly')->setLabel($translator->trans('dashboard.thieunhi_diemgiaoly', [], 'BinhLeAdmin'));
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
	
	private function addBanQuanTriMenuItems($translator, ThanhVien $thanhVien, $params = array()) {
		$phanBo = $thanhVien->getPhanBoNamNay();
	}
	
	private function addThanhVienMenuItems($translator, ThanhVien $thanhVien, $params = array()) {
		$phanBo = $thanhVien->getPhanBoNamNay();
		
		if( ! empty($phanBo)) {
			$chiDoan = $phanBo->getChiDoan();
			
			if($phanBo->isChiDoanTruong()) {
				$this->dauNam->addChild('chia doi trong chi doan', array(
					'route'           => 'admin_app_binhle_thieunhi_chidoan_thieuNhiChiDoanChiaDoi',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chia_doi_chi_doan', [], 'BinhLeAdmin'));
				
				$this->dauNam->addChild('chia doi truong chi doan', array(
					'route'           => 'admin_app_binhle_thieunhi_chidoan_thieuNhiChiDoanChiaTruongPhuTrach',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chia_truong_chi_doan', [], 'BinhLeAdmin'));
				
				$this->dauNam->addChild('bao cao tien quy cho Chi Doan Truong duyet tien quy', array(
					'route'           => 'admin_app_binhle_thieunhi_doinhomgiaoly_baoCaoTienQuy',
					'routeParameters' => [ 'id' => $phanBo->getChiDoan()->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_bao_cao_tien_quy', [], 'BinhLeAdmin'));
				
				$this->diemGiaoLy->addChild('doi nhom giao ly (duyet diem)', array(
					'route'           => 'admin_app_binhle_thieunhi_doinhomgiaoly_list',
					'routeParameters' => [],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_duyet_diem', [], 'BinhLeAdmin'));
				
			}
			
			if( ! empty($this->dauNam)) {
				if($phanBo->getCacTruongPhuTrachDoi()->count() > 0) {
					$this->dauNam->addChild('truong phu trach ghi nhan tien quy', array(
						'route'           => 'admin_app_binhle_thieunhi_tv_truongphutrachdoi_dongQuy',
						'routeParameters' => [ 'id' => $phanBo->getId() ],
						'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
					))->setLabel($translator->trans('dashboard.thieunhi_dong_quy', [], 'BinhLeAdmin'));
				}
			}
			
			if($phanBo->getCacTruongPhuTrachDoi()->count() > 0) {
				$this->diemGiaoLy->addChild('nhap bang diem cho nhom minh', array(
					'route'           => 'admin_app_binhle_thieunhi_phanbo_nhapDiemThieuNhi',
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
				$this->menu->addChild('thieu nhi trong nhom minh', array(
					'route'           => 'admin_app_hoso_thanhvien_thieunhi_thieuNhiNhom',
					'routeParameters' => [ 'phanBo' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_nhomphutrach', [], 'BinhLeAdmin') . $numberStr);
			}
			
			if( ! empty($chiDoan)) {
				$this->menu->addChild('thieu nhi trong Chi-doan minh', array(
					'route'           => 'admin_app_hoso_thanhvien_thieunhi_thieuNhiChiDoan',
					'routeParameters' => [ 'phanBo' => $phanBo->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_chidoanphutrach', [], 'BinhLeAdmin'));
				
				
				$this->menu->addChild('truong chi doan', array(
					'route'           => 'admin_app_binhle_thieunhi_thanhvien_truongChiDoan',
					'routeParameters' => [ 'chiDoan' => $chiDoan->getId() ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_truong_chi_doan', [], 'BinhLeAdmin'));
			}
			
			if($phanBo->isPhanDoanTruongOrSoeur()) {
				$this->dauNam->addChild('phan doan truong duyet tien quy', array(
					'route'           => 'admin_app_binhle_thieunhi_phandoantruong_chidoan_baoCaoTienQuy',
					'routeParameters' => [],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_bao_cao_tien_quy', [], 'BinhLeAdmin'));
				
				$this->diemGiaoLy->addChild('chi doan (duyet diem)', array(
					'route'           => 'admin_app_binhle_thieunhi_phandoantruong_chidoan_list',
					'routeParameters' => [ 'action' => 'duyet-bang-diem' ],
					'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
				))->setLabel($translator->trans('dashboard.thieunhi_duyet_diem', [], 'BinhLeAdmin'));
				
			}
			
			$this->menu->addChild('list thieu nhi toan xu doan', array(
				'route'           => 'admin_app_hoso_thanhvien_thieunhi_list',
//			'routeParameters' => [ 'id' => $salesPartnerId ],
				'labelAttributes' => array( 'icon' => 'fa fa-bar-chart' ),
			))->setLabel($translator->trans('dashboard.list_thieunhi_xudoan', [], 'BinhLeAdmin'));
		}
	}
	
}