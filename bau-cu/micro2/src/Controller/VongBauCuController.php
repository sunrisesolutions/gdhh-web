<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\NhiemKy;
use App\Entity\PhieuBau;
use App\Entity\ViTri;
use Cocur\Slugify\Slugify;
use Doctrine\Common\EventArgs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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

        if (!in_array($nhiemKy->getVongHienTai(), ['xdt1', 'xdt2', 'xdt3', 'xdpNoi', 'xdpNgoai', 'xdpNoi2', 'xdpNgoai2']) && $nhiemKy->getVongHienTai() !== (int) $vong) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        if (count($cacPhieuBau) !== $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $voter->setSubmitted(true);
        $event = new EventArgs();
        $voter->updateData($event, $nhiemKy);
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

    /**
     * @Route("/vong-{vong}/{pin}", name="danh_sach_vong_bau_cu")
     */
    public function danhSachVongBauCu($pin, $vong)
    {
        $repo = $this->getDoctrine()->getRepository(NhiemKy::class);
        $nhiemKy = $repo->findOneByEnabled(true);

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

        if (!in_array($vong, ['xdt1', 'xdt2', 'xdt3', 'xdpNoi', 'xdpNgoai', 'xdpNoi2', 'xdpNgoai2']) && $nhiemKy->getVongHienTai() !== (int) $vong) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        if (count($cacPhieuBau) === $nhiemKy->getRequiredVotes($vong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_review', ['pin' => $pin, 'vong' => $vong]));
        }

        $truong = $repo->getDanhSachUngCu($nhiemKy, $vong);

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

        $cacViTri = $this->getDoctrine()->getRepository(ViTri::class)->findBy(['year' => $nhiemKy->getYear()]);

        return $this->render('pin/vong-bau-cu.html.twig', [
            'controller_name' => 'PinController', 'vong' => $vong,
            'nhiemKy' => $nhiemKy,
            'pin' => $pin,
            'cac_truong' => $truong,
            'cac_phieu_bau' => $phieuBau,
            'cac_vi_tri' => $voter->getCacViTriChuaBau($cacViTri),
        ]);
    }

    /**
     * @Route("/vong-{vong}/{pin}/truong/{truongId}", name="vote_vong_bau_cu_truong")
     */
    public function voteVongBauCuChoTruong($pin, $truongId, $vong, Request $request)
    {
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByEnabled(true);

        if (!in_array($vong, ['xdt1', 'xdt2', 'xdt3', 'xdpNoi', 'xdpNgoai', 'xdpNoi2', 'xdpNgoai2'])) {
            $vong = (int) $vong;
        }
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($nhiemKy) || empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($nhiemKy->getViTri()) {
            if (empty($viTriId = $request->query->get('viTri')) || empty($viTri = $this->getDoctrine()->getRepository(ViTri::class)->find($viTriId))) {
                return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $nhiemKy->getVongHienTai()]));
            };
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong
            ]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if ($vong != $nhiemKy->getVongHienTai()) {
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
        if (!empty($viTri)) {
            $phieuBau->setViTri($viTri);
        }

        $phieuBau
            ->setCuTri($voter)
            ->setHuynhTruong($truong);

        if ($nhiemKy->isVongPhu()) {
            $phieuBau->setVongphu($vong);
        } else {
            $phieuBau->setVong($vong);
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
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($phieuBau);
        $manager->flush();

        return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $vong]));
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

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong]));
        }

        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneBy(['enabled' => true,
        ]);

        if (empty($nhiemKy->getVongHienTai())) {
            return new RedirectResponse($this->generateUrl('vote_vong_bau_cu_result', ['pin' => $pin, 'vong' => $vong]));
        };

        if ($vong != $nhiemKy->getVongHienTai()) {
            return new RedirectResponse($this->generateUrl('danh_sach_vong_bau_cu', ['pin' => $pin, 'vong' => $nhiemKy->getVongHienTai()]));
        };

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findBy(['cuTri' => $voter->getId()], ['createdAt' => 'DESC']);

        if (count($cacPhieuBau) > 0) {
            /** @var PhieuBau $phieuBau */
            $phieuBau = $cacPhieuBau[0];
            if (!empty($phieuBau) && $phieuBau->getVong() != $nhiemKy->getVongHienTai()) {
                return new RedirectResponse($this->generateUrl('pin'));
            }
        }

        return $this->render('pin/review-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController', 'vong' => $vong,
            'pin' => $pin,
            'cac_phieu_bau' => $cacPhieuBau
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
            'quyDinhTop' => $nhiemKy->{'getTopVong'.$vong}(),
            'dangBauCu' => $nhiemKy->dangBauCu(),
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

        $cacPbt = $truong->getCacPhieuBauTheoVong($vong);

        return $this->render('pin/my-vote-for-truong-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'vong' => $vong,
            'truong' => $truong,
            'pin' => $pin,
            'cacPbt' => $cacPbt,
        ]);
    }
}
