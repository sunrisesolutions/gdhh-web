<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\NhiemKy;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ArchivedResultController extends AbstractController
{
    /**
     * @Route("/{year}/vong-{vong}/votes", name="vote_vong_1_votes", requirements={"year"=".+"})
     */
    public function votesVong1($year, $vong)
    {
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByYear($year);
        $quyDinhTop = $nhiemKy->getTopVong1();
        $topVong1 = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['vong'.$vong => 'DESC'], $quyDinhTop);
        $conLai = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['vong'.$vong => 'DESC'], null, $nhiemKy->getTopVong1());

        /** @var HuynhTruong $cuoiTopVong1 */
        $cuoiTopVong1 = end($topVong1);

        /** @var HuynhTruong $dauConLai */
        $dauConLai = array_slice($conLai, 0, 1)[0];

        while ($cuoiTopVong1->getVong1() === $dauConLai->getVong1()) {
            $topVong1[] = $cuoiTopVong1 = array_shift($conLai);
            /** @var HuynhTruong $dauConLai */
            $dauConLai = array_slice($conLai, 0, 1)[0];
        }

        if ($nhiemKy->getVong1phu() && count($topVong1) > $quyDinhTop) {
            /** @var HuynhTruong $cuoiTopVong1 */
            $cuoiTopVong1 = array_pop($topVong1);
            /** @var HuynhTruong $cuoiTopVong1 */
            $keCuoiTopVong1 = end($topVong1);

            $topVong1[] = $cuoiTopVong1;

            $dsPhu = [];
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

        $cacCuTriDaBau = $this->getDoctrine()->getRepository(CuTri::class)->findBy(['submitted' => true, 'year' => $year]);

        return $this->render('pin/vote-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'quyDinhTop' => $nhiemKy->getTopVong1(),
            'year' => $year,
            'cacCuTriDaBau' => $cacCuTriDaBau,
            'topVong1' => $topVong1,
            'dsPhu' => $dsPhu,
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
