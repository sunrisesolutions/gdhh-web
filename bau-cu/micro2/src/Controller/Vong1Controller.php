<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\PhieuBau;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class Vong1Controller
 * @package App\Controller
 * @Route("/vong-1")
 */
class Vong1Controller extends AbstractController
{

    /**
     * @Route("/{pin}/nop-danh-sach", name="vote_vong_1_nop_ds")
     */
    public function nopDanhSachVong1($pin)
    {
        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) !== PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $voter->setSubmitted(true);
        $voter->updateData();
        $m = $this->getDoctrine()->getManager();
        $m->persist($voter);
        /** @var PhieuBau $pb */
        foreach ($voter->getCacPhienBau() as $pb) {
            $m->persist($pb->getHuynhTruong());
        }
        $m->flush();

        return new RedirectResponse($this->generateUrl('vote_vong_1_result', ['pin' => $pin,
        ]));
    }

    /**
     * @Route("/{pin}", name="vote_vong_1")
     */
    public function voteVong1($pin)
    {
        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_result', ['pin' => $pin]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) === PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_review', ['pin' => $pin]));
        }

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['firstName' => 'ASC']);
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

        return $this->render('pin/vong-1.html.twig', [
            'controller_name' => 'PinController',
            'pin' => $pin,
            'cac_truong' => $truong,
            'cac_phieu_bau' => $phieuBau
        ]);
    }

    /**
     * @Route("/{pin}/truong/{truongId}", name="vote_vong_1_truong")
     */
    public function voteVong1ChoTruong($pin, $truongId)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_result', ['pin' => $pin,
            ]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) === PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_review', ['pin' => $pin]));
        }


        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findOneBy(['cuTri' => $voter->getId(), 'huynhTruong' => $truong->getId()]);
        if (!empty($phieuBau)) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $phieuBau = new PhieuBau();
        $phieuBau
            ->setCuTri($voter)
            ->setVong(1)
            ->setHuynhTruong($truong);

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($phieuBau);
        $manager->flush();

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());

        if (count($cacPhieuBau) === PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_review', ['pin' => $pin]));
        }

        return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
    }

    /**
     * @Route("/{pin}/truong/{phieuBauId}/remove-vote", name="vote_vong_1_remove_truong")
     */
    public function removeVoteVong1ChoTruong($pin, $phieuBauId)
    {
        /** @var CuTri $voter */
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        if ($voter->getSubmitted()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_result', ['pin' => $pin,
            ]));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->find($phieuBauId);
        if (empty($phieuBau) || $phieuBau->getCuTri() !== $voter) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $manager = $this->getDoctrine()->getManager();
        $manager->remove($phieuBau);
        $manager->flush();

        return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
    }


    /**
     * @Route("/{pin}/review", name="vote_vong_1_review")
     */
    public function reviewVoteVong1($pin)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $phieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findBy(['cuTri' => $voter->getId()], ['createdAt' => 'DESC']);

        return $this->render('pin/review-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'pin' => $pin,
            'cac_phieu_bau' => $phieuBau
        ]);
    }

    /**
     * @Route("/{pin}/result", name="vote_vong_1_result")
     */
    public function resultVoteVong1($pin)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) !== PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }


        $top25 = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['votes' => 'DESC'], 25);
        $conLai = $this->getDoctrine()->getRepository(HuynhTruong::class)->findBy([], ['votes' => 'DESC'], null, 25);

        return $this->render('pin/result-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'pin' => $pin,
            'top25' => $top25,
            'conLai' => $conLai
        ]);
    }

    /**
     * @Route("/{pin}/truong/{truongId}/my-vote", name="vote_vong_1_my_vote_for_truong")
     */
    public function myVoteForTruong($pin, $truongId)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $truong = $this->getDoctrine()->getRepository(HuynhTruong::class)->find($truongId);
        if (empty($truong)) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) !== PhieuBau::getRequiredVotes()) {
            return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
        }

        $cacPbt = $truong->getCacPhieuBau();

        return $this->render('pin/my-vote-for-truong-vong-1.html.twig', [
            'controller_name' => 'PinController',
            'truong' => $truong,
            'pin' => $pin,
            'cacPbt' => $cacPbt,
        ]);
    }

}