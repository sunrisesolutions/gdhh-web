<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\HuynhTruongXinRut;
use App\Entity\NhiemKy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivedResultController extends AbstractController
{
    /**
     * @Route("/{year}/vong-{vong}/votes", name="vote_vong_bau_cu_votes", requirements={"year"=".+"})
     */
    public function votesVongBauCu($year, $vong)
    {
        $repo = $this->getDoctrine()->getRepository(NhiemKy::class);
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $repo->findOneByYear($year);

        $quyDinhTop = $nhiemKy->{'getTopVong'.$vong}();

        $r = $repo->getKetQuaBauCu($year, $vong);

        $topVong1 = $r['top'];
        $conLai = $r['conLai'];
        $dsRut = $r['rut'];
        $dsPhu = $r['phu'];

        $cacCuTriDaBau = $this->getDoctrine()->getRepository(CuTri::class)->findByCuTriDaBauChoVong($vong, $year);

        return $this->render('pin/vote-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'dangBauCu' => $nhiemKy->dangBauCu(),
            'vong' => $vong,
            'quyDinhTop' =>  $nhiemKy->{'getTopVong'.$vong}(),
            'year' => $year,
            'cacCuTriDaBau' => $cacCuTriDaBau,
            'topVong1' => $topVong1,
            'dsPhu' => $dsPhu,
            'dsRut' => $dsRut,
            'conLai' => $conLai
        ]);
    }

    /**
     * @Route("/{year}/vong-{vong}/truong/{truongId}/votes", name="vote_vong_bau_cu_votes_for_truong", requirements={"year"=".+"})
     */
    public function votesForTruong($year, $truongId, $vong)
    {
//        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong) || $truong->getYear() !== $year) {
            return new RedirectResponse($this->generateUrl('pin', []));
        }

        $cacPbt = $truong->getCacPhieuBauTheoVong($vong);

        return $this->render('pin/votes-for-truong-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'truong' => $truong,
            'cacPbt' => $cacPbt,
        ]);
    }
}
