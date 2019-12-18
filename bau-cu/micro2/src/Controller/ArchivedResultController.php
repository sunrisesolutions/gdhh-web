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
        /** @var NhiemKy $nhiemKy */
        $nhiemKy = $this->getDoctrine()->getRepository(NhiemKy::class)->findOneByYear($year);
        $quyDinhTop = $nhiemKy->{'getTopVong'.$vong}();
        $topVong1 = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vong => 'DESC', 'vong'.$vong.'phu' => 'DESC'], $quyDinhTop);
        $conLai = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy(['enabled' => true, 'year' => $year], ['vong'.$vong => 'DESC'], null, $nhiemKy->{'getTopVong'.$vong}());
        $dsRut = $this->getDoctrine()->getRepository(HuynhTruongXinRut::class)->findBy(['year' => $year, 'vong' => $vong]);

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

        $cacCuTriDaBau = $this->getDoctrine()->getRepository(CuTri::class)->findBy(['submitted' => true, 'year' => $year]);

        return $this->render('pin/vote-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'vong' => $vong,
            'quyDinhTop' => $nhiemKy->getTopVong1(),
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

        $cacPbt = $truong->getCacPhieuBau();

        return $this->render('pin/votes-for-truong-vong-bau-cu.html.twig', [
            'controller_name' => 'PinController',
            'truong' => $truong,
            'cacPbt' => $cacPbt,
        ]);
    }
}
