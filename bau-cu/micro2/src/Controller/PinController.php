<?php

namespace App\Controller;

use App\Entity\CuTri;
use App\Entity\HuynhTruong;
use App\Entity\PhieuBau;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class PinController extends AbstractController
{
    /**
     * @Route("/", name="pin")
     */
    public function index()
    {
        return $this->render('pin/index.html.twig', [
            'controller_name' => 'PinController',
        ]);
    }

    /**
     * @Route("/vong-1/{pin}", name="vote_vong_1")
     */
    public function voteVong1($pin)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) === PhieuBau::VOTER_VOTES) {
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
     * @Route("/vong-1/{pin}/truong/{truongId}", name="vote_vong_1_truong")
     */
    public function voteVong1ChoTruong($pin, $truongId)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        $cacPhieuBau = $this->getDoctrine()->getRepository(PhieuBau::class)->findByCuTri($voter->getId());
        if (count($cacPhieuBau) === PhieuBau::VOTER_VOTES) {
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

        if (count($cacPhieuBau) === PhieuBau::VOTER_VOTES) {
            return new RedirectResponse($this->generateUrl('vote_vong_1_review', ['pin' => $pin]));
        }

        return new RedirectResponse($this->generateUrl('vote_vong_1', ['pin' => $pin]));
    }

    /**
     * @Route("/vong-1/{pin}/truong/{phieuBauId}/remove-vote", name="vote_vong_1_remove_truong")
     */
    public function removeVoteVong1ChoTruong($pin, $phieuBauId)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
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
     * @Route("/vong-1/{pin}/review", name="vote_vong_1_review")
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
     * @Route("/vong-1/{pin}/result", name="vote_vong_1_result")
     */
    public function resultVoteVong1($pin)
    {
        $voter = $this->getDoctrine()->getRepository(CuTri::class)->findOneByPin($pin);
        if (empty($voter)) {
            return new RedirectResponse($this->generateUrl('pin'));
        }

        return $this->render('pin/result-vong-1.html.twig', [
            'controller_name' => 'PinController',
        ]);
    }


    /**
     * @Route("/vong-1/{pin}/votes", name="vote_vong_1_votes")
     */
    public function votesVong1($pin)
    {
        return $this->render('pin/vote-vong-1.html.twig', [
            'controller_name' => 'PinController',
        ]);
    }
}
