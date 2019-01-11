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
use App\Service\Data\SpreadsheetWriter;
use App\Service\HoSo\ThanhVienService;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class TruongPhuTrachDoiAdminController extends BaseCRUDAdminController
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
        
        $phanBoThieuNhi = $phanBo->getCacPhanBoThieuNhiPhuTrach();
        
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
        
        if ($phanBo->coTheNopBangDiem($hocKy)) {
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
            foreach ($cacDccTheoThang as $cacDcc) {
                /** @var PhanBo $pb */
                foreach ($phanBoThieuNhi as $pb) {
                    $bangDiem = $pb->getBangDiem();
                    $bangDiem->tinhDiemChuyenCanThang($cacDcc);
                    $bangDiem->setSundayTicketTerm1(0);
                    $bangDiem->setSundayTicketTerm2(0);
                    $bangDiem->setSundayTickets(0);
                    $bangDiem->tinhPhieuLeCNThang($cacDcc);
                    $manager->persist($bangDiem);
                }
            }
            
            $manager->flush();
            
            $this->admin->getModelManager()->update($phanBo);
        }
        
        return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_truongphutrachdoi_nhapDiemThieuNhi', ['id' => $id]));
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
        
        /** @var TruongPhuTrachDoiAdmin $admin */
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
        
        if ($phanBo->coTheNopBangDiem($hocKy)) {
            $hoanTatBangDiemHKMethod = 'hoanTatBangDiemHK' . $hocKy;
            $phanBo->$hoanTatBangDiemHKMethod();
            $this->admin->getModelManager()->update($phanBo);
        }
        
        return new RedirectResponse($this->generateUrl('admin_app_hoso_phanbo_truongphutrachdoi_nhapDiemThieuNhi', ['id' => $id]));
    }
    
    public function dongQuyAction($id = null, Request $request)
    {
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the PhanBo with id : %s', $id));
        }
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        $manager = $this->get('doctrine.orm.default_entity_manager');
        
        if ($request->isMethod('post')) {
            $diem = floatval($request->request->get('diem', 0));
            $soTien = $request->request->getInt('soTien', 0);
            $dongQuy = $request->request->getBoolean('dongQuy', false);
            $ngheoKho = $request->request->getBoolean('ngheoKho', false);
            
            $phanBoId = $request->request->get('phanBoId');
            $phanBo = $this->getDoctrine()->getRepository(PhanBo::class)->find($phanBoId);
            
            if (($dongQuy === false && $soTien === 0 || $dongQuy === true && $soTien > 0 || !empty($phanBo))) {
                
                $phanBo->setTienQuyDong($soTien);
                $phanBo->setDaDongQuy($dongQuy);
                $phanBo->setNgheoKho($ngheoKho);
                $tv = $phanBo->getThanhVien();
                $tv->setNgheoKho($ngheoKho);
                
                $manager->persist($phanBo);
                $manager->persist($tv);
                $manager->flush();

//
                return new JsonResponse(['OK'], 200);
            } else {
                return new JsonResponse([404, 'Không thể tìm thấy Thiếu-nhi này'], 404);
            }
        }
        
        $manager->persist($phanBo);
        $manager->flush();
        
        
        $phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
        
        $admin->namHoc = $phanBo->getNamHoc();
        $admin->setAction('dong-quy');
        $admin->setActionParams([
            'chiDoan' => $phanBo->getChiDoan(),
            'phanBoHangNam' => $phanBoHangNam,
            'christianNames' => ThanhVien::$christianNames
        ]);
        
        return parent::listAction();
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
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        $manager = $this->get('doctrine.orm.default_entity_manager');
        /** @var User $user */
        $user = $this->getUser();
        $thanhVien = $user->getThanhVien();;
        if (empty($_phanBo = $thanhVien->getPhanBoNamNay())) {
            throw new NotFoundHttpException('No Group Assignment found');
        }
        
        if ($_phanBo->getId() !== $phanBo->getId()) {
            if (!$_phanBo->quanLy($phanBo)) {
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
        
        $phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
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
            'downloadHk1Url' => $this->get('router')->generate('admin_app_hoso_phanbo_truongphutrachdoi_thieuNhiNhomDownloadBangDiem',
                [
                    'id' => $phanBo->getId(),
                    'hocKy' => 1
                ]
            ),
            'downloadHk2Url' => $this->get('router')->generate('admin_app_hoso_phanbo_truongphutrachdoi_thieuNhiNhomDownloadBangDiem',
                [
                    'id' => $phanBo->getId(),
                    'hocKy' => 2
                ]
            ),
            'nopDiemUrl' =>
                $this->get('router')->generate('admin_app_hoso_phanbo_truongphutrachdoi_nopBangDiem',
                    [
                        'id' => $phanBo->getId(),
                        'hocKy' => $hocKy
                    ]
                )
        ]);
        
        return parent::listAction();
    }
    
    public function diemDanhThu5Action($id = null, Request $request)
    {
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        
        $manager = $this->get('doctrine.orm.default_entity_manager');
        /** @var User $user */
        $user = $this->getUser();
        $thanhVien = $user->getThanhVien();;
        if (empty($_phanBo = $thanhVien->getPhanBoNamNay())) {
            throw new NotFoundHttpException('No Group Assignment found');
        }
        
        if ($_phanBo->getId() !== $phanBo->getId()) {
            if (!$_phanBo->quanLy($phanBo)) {
                throw new UnauthorizedHttpException('not authorised');
            }
        }
        
        $phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
        $manager->persist($phanBo);
        $manager->flush();
        
        
        $admin->setTemplate('list', 'admin/truong-phu-trach-doi/list-diem-danh-t5.html.twig');
        $admin->setTemplate('inner_list_row', 'admin/truong-phu-trach-doi/list_inner_row-diem-danh-t5.html.twig');
        
        $admin->setAction('diem-danh-t5');
        
        $admin->setActionParams([
            'chiDoan' => $phanBo->getChiDoan(),
            'phanBo' => $phanBo,
            'phanBoHangNam' => $phanBoHangNam,
            'christianNames' => ThanhVien::$christianNames,
        ]);
        
        return parent::listAction();
    }
    
    
    public function diemDanhChuaNhatAction($id = null, Request $request)
    {
        /**
         * @var PhanBo $phanBo
         */
        $phanBo = $this->admin->getSubject();
        if (!$phanBo) {
            throw new NotFoundHttpException(sprintf('unable to find the Truong with id : %s', $id));
        }
        
        /** @var TruongPhuTrachDoiAdmin $admin */
        $admin = $this->admin;
        
        
        $manager = $this->get('doctrine.orm.default_entity_manager');
        /** @var User $user */
        $user = $this->getUser();
        $thanhVien = $user->getThanhVien();;
        if (empty($_phanBo = $thanhVien->getPhanBoNamNay())) {
            throw new NotFoundHttpException('No Group Assignment found');
        }
        
        if ($_phanBo->getId() !== $phanBo->getId()) {
            if (!$_phanBo->quanLy($phanBo)) {
                throw new UnauthorizedHttpException('not authorised');
            }
        }
        
        $cotDiemHeaders = [];
        $cotDiemAttrs = [];
        $cotDiemLabels = [];
        $cotDiemCellFormats = [];
        
        $phanBoHangNam = $phanBo->getCacPhanBoThieuNhiPhuTrach();
        $manager->persist($phanBo);
        $manager->flush();
        
        
        $admin->setTemplate('list', 'admin/truong-phu-trach-doi/list-diem-danh-cn.html.twig');
        $admin->setTemplate('inner_list_row', 'admin/truong-phu-trach-doi/list_inner_row-diem-danh-cn.html.twig');
        
        $admin->setAction('diem-danh-cn');
        $admin->setActionParams([
            'chiDoan' => $phanBo->getChiDoan(),
            'phanBo' => $phanBo,
            'phanBoHangNam' => $phanBoHangNam,
            'christianNames' => ThanhVien::$christianNames,
        ]);
        
        return parent::listAction();
    }
    
    public function thieuNhiNhomDownloadBangDiemAction($id = null, $hocKy, Request $request)
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
        
        $huynhTruong = $phanBo->getThanhVien()->getHuynhTruongObj();
        
        $response = $huynhTruong->downloadBangDiemExcel($hocKy);
        
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        
        $filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        
        return $response;
    }
    
    public function thieuNhiNhomDownloadBangDiemActionBKK($id = null, $hocKy, Request $request)
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
        
        $thanhVienService = $this->get(ThanhVienService::class);
        
        $filename = sprintf('bang-diem-hoc-ky-%d.xlsx', $hocKy);
//		$response = new BinaryFileResponse($zipFile);
//		$response->headers->set('Content-Disposition', 'attachment;filename=' . str_replace(' ', '-', 'ihp_export_' . $dateAlnum . '.zip'));
        $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
        
        $phpExcelObject->getProperties()->setCreator("Solution")
            ->setLastModifiedBy("Solution")
            ->setTitle("Download - Raw Data")
            ->setSubject("Bang Diem")
            ->setDescription("Raw Data")
            ->setKeywords("office 2005 openxml php")
            ->setCategory("Raw Data Download");
        
        $phpExcelObject->setActiveSheetIndex(0);
        $activeSheet = $phpExcelObject->getActiveSheet();
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $phpExcelObject->setActiveSheetIndex(0);
        
        
        $sWriter = new SpreadsheetWriter($activeSheet);
        $thanhVienService->writeBangDiemDoiNhomGiaoLyHeading($sWriter, $hocKy, $phanBo);
        
        if ($hocKy === 1) {
            foreach (range('A', 'N') as $columnID) {
                $activeSheet->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
        }
        
        if ($hocKy === 1) {
            $thanhVienService->writeBangDiemDoiNhomGiaoLyHK1Data($sWriter, $phanBo);
        }

//		$colDimensions = $activeSheet->getColumnDimensions();
//		foreach($colDimensions as $dimension) {
//			$dimension->setAutoSize(true);
//		}
        
        $activeSheet->calculateColumnWidths();
        // create the writer
        $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel2007');
        // create the response
        $response = $this->get('phpexcel')->createStreamedResponse($writer);
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');
        
        $response->headers->set('Content-Disposition', 'attachment;filename=' . $filename);
        
        return $response;
    }
    
}
