<?php

namespace App\Service\HocBa;

use App\Entity\HoSo\DiemChuyenCan;
use App\Entity\HoSo\PhanBo;
use App\Service\BaseService;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BangDiemService extends BaseService
{

    public function tinhDiemChuyenCan($phanBoThieuNhi, $hocKy = 1)
    {
        $tatCaDcc = $this->getDoctrine()->getRepository(DiemChuyenCan::class)->findAll();
        $cacDccTheoThang = [];
        /** @var DiemChuyenCan $dcc */
        foreach ($tatCaDcc as $dcc) {
            $date = $dcc->getTargetDate();
            $dateNumber = (int) $date->format('m');
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
            if (empty($pb->getChiDoan())) {
                continue;
            }
            $bangDiem = $pb->getBangDiem();
            $bangDiem->setSundayTicketTerm1(0);
            $bangDiem->setSundayTicketTerm2(0);
            $bangDiem->setSundayTickets(0);
            foreach ($cacDccTheoThang as $cacDcc) {
                $bangDiem->tinhDiemChuyenCanThang($cacDcc);
                $bangDiem->tinhPhieuLeCNThang($cacDcc);
            }
            $bangDiem->tinhDiemChuyenCan($hocKy);
            $bangDiem->tinhDiemHocKy($hocKy);
            $manager->persist($bangDiem);

        }

        $manager->flush();

    }
}