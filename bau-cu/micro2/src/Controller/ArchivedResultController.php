<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivedResultController extends AbstractController
{
    /**
     * @Route("/{year}/vong-1/votes", name="vote_vong_1_votes", requirements={"year"=".+"})
     */
    public function votesVong1($year)
    {
        $top25 = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['votes' => 'DESC'], 25);
        $conLai = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['votes' => 'DESC'], null, 25);
        $cacCuTriDaBau = $this->getDoctrine()->getRepository(CuTri::class)->findBy(['submitted' => true, 'year' => $year]);

        return $this->render('pin/vote-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'year' => $year,
            'cacCuTriDaBau' => $cacCuTriDaBau,
            'top25' => $top25,
            'conLai' => $conLai
        ]);
    }

    /**
     * @Route("/{year}/vong-1/truong/{truongId}/votes", name="vote_vong_1_votes_for_truong", requirements={"year"=".+"})
     */
    public function votesForTruong($year, $truongId)
    {
//        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong) || $truong->getYear() !== $year) {
            return new RedirectResponse($this->generateUrl('pin', []));
        }

        $cacPbt = $truong->getCacPhieuBau();

        return $this->render('pin/votes-for-truong-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'truong' => $truong,
            'cacPbt' => $cacPbt,
        ]);
    }
}
