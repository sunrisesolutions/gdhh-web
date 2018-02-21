<?php
namespace App\Admin\HoSo\ThanhVien;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\PhanBo;
use App\Service\User\UserService;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class HuynhTruongAdminController extends BaseCRUDAdminController
{
	public function thieuNhiNhomAction(PhanBo $phanBo, Request $request) {
		/** @var ThanhVienAdmin $admin */
		$admin = $this->admin;
		
		$cacTruongPT      = $phanBo->getCacTruongPhuTrachDoi();
		$cacDoiNhomGiaoLy = [];
		
		/** @var TruongPhuTrachDoi $truongPT */
		foreach($cacTruongPT as $truongPT) {
			$cacDoiNhomGiaoLy [] = $truongPT->getDoiNhomGiaoLy();
		}
		
		$admin->setAction('list-thieu-nhi-nhom');
		$admin->setActionParams([
			'phanBo'           => $phanBo,
			'cacDoiNhomGiaoLy' => $cacDoiNhomGiaoLy,
			'chiDoan'          => $phanBo->getChiDoan()
		]);
		
		return parent::listAction();
	}
	
}