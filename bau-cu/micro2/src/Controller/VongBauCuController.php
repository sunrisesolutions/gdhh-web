<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\NhiemKy;
use App\Entity\PhieuBau;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class VongBauCuController
 * @package App\Controller
 */
class VongBauCuController extends AbstractController
{

    /**
     * @Route("/vong-{vong}/{pin}/nop-danh-sach", name="vote_vong_bau_cu_nop_ds")
     */
    public function nopDanhSachVong($pin, $vong)
    {
        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if ($nhiemKy->getVongHienTai() !== (int) $vong) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        if (count($cacPhieuBau) !== $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $voter->setSubmitted(true);
//        $event = new EventArgs();
//        $voter->updateData($event, $nhiemKy);
        $m = $this->getDoctrine()->getManager();
        $m->persist($voter);
        /** @var PhieuBau $pb */
        foreach ($voter->getCacPhienBau() as $pb) {
            $m->persist($pb->getHuynhTruong());
        }
        $m->flush();

        return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong
        ]));
    }

    public function getDanhSachUngCu()
    {
        $year = $nhiemKy->getYear();
        if ($vong > 1) {
            $vongTruoc = $vong - 1;
            $quyDinhTop = $nhiemKy->{'getTopVong'.$vongTruoc}();

            $topVong1 = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vongTruoc => 'DESC', 'vong'.$vongTruoc.'phu' => 'DESC'], $quyDinhTop);
            $conLai = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vongTruoc => 'DESC'], null, $nhiemKy->{'getTopVong'.$vongTruoc}());

            /** @var HuynhTruong $cuoiTopVong1 */
            $cuoiTopVong1 = end($topVong1);
            if (count($conLai) > 0) {
                /** @var HuynhTruong $dauConLai */
                $dauConLai = array_slice($conLai, 0, 1)[0];
                $dsPhu = [];

                if ($nhiemKy->getVong1phu() || $nhiemKy->getVong1phu() === null) {
                    while ($cuoiTopVong1->getVong1() === $dauConLai->getVong1()) {
                        $topVong1[] = $cuoiTopVong1 = array_shift($conLai);
                        /** @var HuynhTruong $dauConLai */
                        $dauConLai = array_slice($conLai, 0, 1)[0];
                    }
                }
                if ($nhiemKy->getVong1phu()) {
                    if (count($topVong1) > $quyDinhTop) {
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $cuoiTopVong1 = array_pop($topVong1);
                        /** @var HuynhTruong $cuoiTopVong1 */
                        $keCuoiTopVong1 = end($topVong1);

                        $topVong1[] = $cuoiTopVong1;

                        while ($cuoiTopVong1->getVong1() === $keCuoiTopVong1->getVong1()) {
                            /** @var HuynhTruong $cuoiTopVong1 */
                            $dsPhu[] = $cuoiTopVong1 = array_pop($topVong1);
                            /** @var HuynhTruong $cuoiTopVong1 */
                            $keCuoiTopVong1 = end($topVong1);
                        }
                        if (count($dsPhu) === 0) {
                            $topVong1[] = $cuoiTopVong1;
                        }
                    }
                }
            }

            $truong = $topVong1;
            usort($truong, function ($a, $b) {
                $s = Slugify::create();
                return strcmp($s->slugify($a->getFirstName()), $s->slugify($b->getFirstName()));
            });

        } else {
            $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['firstName' => 'ASC'], $nhiemKy->{'getTopVong'.$vong}());
        }
        return $truong;
    }

    /**
     * @Route("/vong-{vong}/{pin}", name="danh_sach_vong_bau_cu")
     */
    public function danhSachVongBauCu($pin, $vong)
    {
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByEnabled(true);

        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($nhiemKy) || empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong]));
        }


        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if ($nhiemKy->getVongHienTai() !== (int) $vong) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        if (count($cacPhieuBau) === $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_review', ['pin' => $pin, 'vong' => $vong]));
        }

        $truong = $this->getDanhSachUngCu();

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findBy(['cuTri' => $voter->getId()], ['createdAt' => 'DESC']);
        if (count($phieuBau) > 0) {
            /** @var PhieuBau $pb */
            foreach ($phieuBau as $pb) {
                $hTruong = $pb->getHuynhTruong();
                /**
                 * @var  $i
                 * @var HuynhTruong $value
                 */
                foreach ($truong as $i => $value) {
                    if ($hTruong === $value) {
                        array_splice($truong, $i, 1);
                    }
                }
            }
        }

        return $this->render('pin/vong-bau-cu.html.twig', [
            'controller_name' => 'PinController', 'vong' => $vong,
            'pin' => $pin,
            'cac_truong' => $truong,
            'cac_phieu_bau' => $phieuBau
        ]);
    }

    /**
     * @Route("/vong-{vong}/{pin}/truong/{truongId}", name="vote_vong_bau_cu_truong")
     */
    public function voteVongBauCuChoTruong($pin, $truongId, $vong)
    {
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByEnabled(true);

        $vong = (int) $vong;
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($nhiemKy) || empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong
            ]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if ((int) $vong !== $nhiemKy->getVongHienTai()) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $nhiemKy->getVongHienTai()]));
        };

        if (count($cacPhieuBau) === $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_review', ['pin' => $pin, 'vong' => $vong]));
        }

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findOneBy(['cuTri' => $voter->getId(), 'huynhTruong' => $truong->getId()]);
        if (!empty($phieuBau)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $phieuBau = new PhieuBau();
        $phieuBau
            ->setCuTri($voter)
            ->setVong($vong)
            ->setHuynhTruong($truong);

        if ($nhiemKy->isVongPhu()) {
            $phieuBau->setVongphu($vong);
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($phieuBau);
        $manager->flush();

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        if (count($cacPhieuBau) === $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_review', ['pin' => $pin, 'vong' => $vong]));
        }

        return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
    }

    /**
     * @Route("/vong-{vong}/{pin}/truong/{phieuBauId}/remove-vote", name="vote_vong_bau_cu_remove_truong")
     */
    public function removeVoteVongBauCuChoTruong($pin, $phieuBauId, $vong)
    {
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByEnabled(true);

        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($nhiemKy) || empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong
            ]));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->find($phieuBauId);
        if (empty($phieuBau) || $phieuBau->getCuTri() !== $voter) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin]));
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($phieuBau);
        $manager->flush();

        return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin]));
    }


    /**
     * @Route("/vong-{vong}/{pin}/review", name="vote_vong_bau_cu_review")
     */
    public function reviewVoteVong($pin, $vong)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findBy(['cuTri' => $voter->getId()], ['createdAt' => 'DESC']);

        return $this->render('pin/review-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController', 'vong' => $vong,
            'pin' => $pin,
            'cac_phieu_bau' => $phieuBau
        ]);
    }

    /**
     * @Route("/vong-{vong}/{pin}/result", name="vote_vong_bau_cu_result")
     */
    public function resultVoteVong($pin, $vong)
    {
        $repo = $this->getDoctrine()->getRepository(NhiemKy::class);
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $repo->findOneByEnabled(true);
        $year = $nhiemKy->getYear();

        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if (count($cacPhieuBau) !== $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $r = $repo->getKetQuaBauCu($year, $vong);

        $topVong = $r['top'];
        $conLai = $r['conLai'];
        $dsRut = $r['rut'];
        $dsPhu = $r['phu'];

        return $this->render('pin/result-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController', 'vong' => $vong,
            'pin' => $pin,
            'top25' => $topVong,
            'conLai' => $conLai
        ]);
    }

    /**
     * @Route("/vong-{vong}/{pin}/truong/{truongId}/my-vote", name="vote_vong_bau_cu_my_vote_for_truong")
     */
    public function myVoteForTruong($pin, $truongId, $vong)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if (count($cacPhieuBau) !== $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $cacPbt = $truong->getCacPhieuBau();

        return $this->render('pin/my-vote-for-truong-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'vong' => $vong,
            'truong' => $truong,
            'pin' => $pin,
            'cacPbt' => $cacPbt,
        ]);
    }
}
