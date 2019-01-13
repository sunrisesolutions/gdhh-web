<?php

namespace App\Admin\HoSo\PhanBo;

use App\Admin\BaseCRUDAdminController;
use App\Entity\HoSo\ChiDoan;
use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\DoiNhomGiaoLy;
use App\Entity\HoSo\PhanBo;
use App\Entity\HoSo\ThanhVien;
use App\Entity\HoSo\TruongPhuTrachDoi;
use App\Entity\User\User;
use App\Helper\BangDiemHandsonTableService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class ThuKyChiDoanAdminController extends BaseCRUDAdminController
{
    
    public function tinhDiemChuyenCanAction($id = null, $hocKy, Request $request)
    {
        if (!in_array($hocKy, [1, 2])) {
            throw new \InvalidArgumentException();
        }
        
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        $thuKy = $phanBo->getThanhVien()->getThuKyChiDoanObj();
        
        $phanBoThieuNhi = $thuKy->getCacPhanBoThieuNhiPhuTrach();
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        $chiDoan = $phanBo->getChiDoan();
        
        $admin->setAction('tinh-diem-chuyen-can');
        $admin->setActionParams([
            'chiDoan' => $chiDoan,
            'christianNames' => ThanhVien::$christianNames,
            'hocKy' => $hocKy
        ]);
        
        if (!$admin->isGranted('NOP_BANG_DIEM', $phanBo)) {
            throw new AccessDeniedHttpException();
        }
        
        
        if ($thuKy->coTheNopBangDiem($hocKy)) {
            $tatCaDcc = $this->getDoctrine()->getRepository(DiemChuyenCan::class)->findAll();
            $cacDccTheoThang = [];
            /** @var DiemChuyenCan $dcc */
            foreach ($tatCaDcc as $dcc) {
                $date = $dcc->getTargetDate();
                $dateNumber = (int)$date->format('m');
                if ($hocKy === 1) {
                    if ($dateNumber < 9 || $dateNumber > 12) {
                        continue;
                    }
                }
                if (!isset($cacDccTheoThang[$dateNumber]) || !is_array($cacDccTheoThang[$dateNumber])) {
                    $cacDccTheoThang[$dateNumber] = [];
                }
                $cacDccTheoThang[$dateNumber][] = $dcc;
            }
            
            $manager = $this->get('doctrine.orm.default_entity_manager');
            
            /** @var PhanBo $pb */
            foreach ($phanBoThieuNhi as $pb) {
                $bangDiem = $pb->getBangDiem();
                $bangDiem->setSundayTicketTerm1(0);
                $bangDiem->setSundayTicketTerm2(0);
                $bangDiem->setSundayTickets(0);
                foreach ($cacDccTheoThang as $cacDcc) {
                    $bangDiem->tinhDiemChuyenCanThang($cacDcc);
                    $bangDiem->tinhDiemChuyenCan($hocKy);
                    $bangDiem->tinhDiemGiaoLy($hocKy);
                    $bangDiem->tinhPhieuLeCNThang($cacDcc);
                }
                $manager->persist($bangDiem);
                
            }
            
            $manager->flush();
            
            $this->admin->getModelManager()->update($phanBo);
        }
        
        return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_thukychidoan_nhapDiemThieuNhi', ['id' => $id]));
    }
    
    public function nopBangDiemAction($id = null, $hocKy, Request $request)
    {
        if (!in_array($hocKy, [1, 2])) {
            throw new \InvalidArgumentException();
        }
        
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        /** @var ThuKyChiDoanAdmin $admin */
        $admin = $this->admin;
        
        $chiDoan = $phanBo->getChiDoan();
        
        $admin->setAction('nop-bang-diem');
        $admin->setActionParams([
            'chiDoan' => $chiDoan,
            'christianNames' => ThanhVien::$christianNames,
            'hocKy' => $hocKy
        ]);
        
        if (!$admin->isGranted('NOP_BANG_DIEM', $phanBo)) {
            throw new AccessDeniedHttpException();
        }
        $thuKy = $phanBo->getThanhVien()->getThuKyChiDoanObj();
        
        if ($thuKy->coTheNopBangDiem($hocKy)) {
            $hoanTatBangDiemHKMethod = 'hoanTatBangDiemHK' . $hocKy;
            $chiDoan->$hoanTatBangDiemHKMethod(true);
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($chiDoan);
            $manager->flush();
        }
        
        return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_thukychidoan_nhapDiemThieuNhi', ['id' => $id]));
    }
    
    public function nhapDiemThieuNhiAction($id = null, Request $request)
    {
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        /** @var PhanBoAdmin $admin */
        $admin = $this->admin;
        
        $manager = $this->get('doctrine.orm.default_entity_manager');
        /** @var User $user */
        $user = $this->getUser();
        $thanhVien = $user->getThanhVien();;
        if (empty($_phanBo = $thanhVien->getPhanBoNamNay())) {
            throw new NotFoundHttpException('No Group Assignment found');
        }
        
        if ($_phanBo->getId() !== $phanBo->getId()) {
            if (!$_phanBo->getThanhVien()->getThuKyChiDoanObj()->isThieuNhiCungChiDoan($phanBo)) {
                throw new UnauthorizedHttpException('not authorised');
            }
        }
        
        $cotDiemHeaders = [];
        $cotDiemAttrs = [];
        $cotDiemLabels = [];
        $cotDiemCellFormats = [];
        $bangDiemHelper = $this->get(BangDiemHandsonTableService::class);
        $result = $bangDiemHelper->prepareTable($phanBo, $cotDiemHeaders, $cotDiemAttrs, $cotDiemLabels, $cotDiemCellFormats);
        
        $readOnly = $result ['readOnly'];
        $hocKy = $result['hocKy'];
        
        if ($request->isMethod('post')) {
            return $bangDiemHelper->ghiDiem($request, $cotDiemHeaders, $cotDiemAttrs, $cotDiemLabels, $cotDiemCellFormats, $result);
        }
        
        $phanBoHangNam = $phanBo->getThanhVien()->getThuKyChiDoanObj()->getCacPhanBoThieuNhiPhuTrach();
        
        $manager->persist($phanBo);
        $manager->flush();
        
        $admin->setAction('nhap-diem-thieu-nhi');
        $admin->setActionParams([
            'chiDoan' => $phanBo->getChiDoan(),
            'phanBo' => $phanBo,
            'phanBoHangNam' => $phanBoHangNam,
            'hocKy' => $hocKy,
            'cotDiemHeaders' => $cotDiemHeaders,
            'cotDiemAttrs' => $cotDiemAttrs,
            'cotDiemLabels' => $cotDiemLabels,
            
            'cotDiemCellFormats' => $cotDiemCellFormats,
            'christianNames' => ThanhVien::$christianNames,
            'downloadHk1Url' => $this->get('router')->generate('admin_app_hoso_phanbo_thukychidoan_thieuNhiChiDoanDownloadBangDiem',
                [
                    'id' => $phanBo->getId(),
                    'hocKy' => 1
                ]
            ),
            'downloadHk2Url' => $this->get('router')->generate('admin_app_hoso_phanbo_thukychidoan_thieuNhiChiDoanDownloadBangDiem',
                [
                    'id' => $phanBo->getId(),
                    'hocKy' => 2
                ]
            ),
            
            'nopDiemUrl' =>
                $this->get('router')->generate('admin_app_hoso_phanbo_thukychidoan_nopBangDiem',
                    [
                        'id' => $phanBo->getId(),
                        'hocKy' => $hocKy
                    ]
                )
        ]);
        
        return parent::listAction();
    }
    
    public function thieuNhiChiDoanDownloadBangDiemAction($id = null, $hocKy, Request $request)
    {
        if (!in_array($hocKy, [1, 2])) {
            throw new \InvalidArgumentException();
        }
        
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        //		\PHPExcel_Shared_Font::setAutoSizeMethod(\PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);
        $hocKy = intval($hocKy);
        
        $thuKyCD = $phanBo->getThanhVien()->getThuKyChiDoanObj();
        
        $response = $thuKyCD->downloadBangDiemExcel($hocKy);
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        
        $filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        
        return $response;
    }
    
}