<?php
namespace App\Admin\HoSo\DoiNhomGiaoLy;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\DoiNhomGiaoLy;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

use App\Admin\HoSo\ChiDoan\ChiDoanTruongChiDoanAdmin;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;

use Sonata\AdminBundle\Controller\CRUDController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\InvalidArgumentException;

class TruongPhuTrachDnglAdminController extends BaseCRUDAdminController {
	
	public function baoCaoTienQuyAction(Request $request) {
		/** @var DoiNhomGiaoLyAdmin $admin */
		$admin = $this->admin;

//		if( ! empty($namHoc = $this->get('app.binhle_thieunhi_namhoc')->getNamHocHienTai())) {
//			$admin->setNamHoc($namHoc->getId());
//		}
		
		$admin->setAction('bao-cao-tien-quy');
		
		return parent::listAction();
		
	}
	
	public function bangDiemAction($id = null, $hocKy = null, $action = null, Request $request) {
		/**
		 * @var DoiNhomGiaoLy $dngl
		 */
		$dngl = $this->admin->getSubject();
		if( ! $dngl) {
			throw new NotFoundHttpException(sprintf('Unable to find the DoiNhomGiaoLy with id : %s', $id));
		}
		
		/** @var DoiNhomGiaoLyAdmin $admin */
		$admin = $this->admin;
		
		if( ! in_array($action, [ 'duyet', 'tra-ve' ])) {
			throw new InvalidArgumentException();
		}
		
		if( ! in_array(intval($hocKy), [ 1, 2 ])) {
			throw new InvalidArgumentException();
		}
		
		$manager = $this->get('doctrine.orm.entity_manager');
		
		$setterDuyetBandDiemMethod   = 'setDuyetBangDiemHK' . $hocKy . 'CDT';
		$setterHoanTatBandDiemMethod = 'setHoanTatBangDiemHK' . $hocKy;
		$hoanTatBandDiemMethod       = 'hoanTatBangDiemHK' . $hocKy;
		if($action === 'duyet') {
			$dngl->$setterDuyetBandDiemMethod(true);
			if($dngl->getChiDoan()->$hoanTatBandDiemMethod()) {
				$manager->persist($dngl->getChiDoan());
			};
		} elseif($action === 'tra-ve') {
			$dngl->$setterDuyetBandDiemMethod(false);
			$dngl->$setterHoanTatBandDiemMethod(false);
		}
		
		try {
			$manager->persist($dngl);
			$manager->flush();
			if($action === 'duyet') {
				$this->addFlash('sonata_flash_success', sprintf("Bảng điểm đội %s đã được duyệt.", $dngl->getNumber()));
			} elseif($action === 'tra-ve') {
				$this->addFlash('sonata_flash_success', sprintf("Bảng điểm đội %s đã bị trả về.", $dngl->getNumber()));
			}
			
		} catch(\Exception $exception) {
			$this->addFlash('sonata_flash_error', $exception->getMessage());
		}
		
		return new RedirectResponse($this->generateUrl('admin_app_binhle_thieunhi_doinhomgiaoly_list', []));
	}
}