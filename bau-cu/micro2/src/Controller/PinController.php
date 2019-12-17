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
     * @Route("/reset-cutri-voters-nhe", name="pin_reset-cutri-voters-nhe")
     */
    public function resetCuTriVoters()
    {
//        $voters = $this->getDoctrine()->getRepository(CuTri::class)->findAll();
//        foreach ($voters as $voter) {
//            $voter->setSubmitted(false);
//            $this->getDoctrine()->getManager()->persist($voter);
//        }
//
//        $pbs = $this->getDoctrine()->getRepository(PhieuBau::class)->findAll();
//        foreach ($pbs as $pb) {
//            $truong = $pb->getHuynhTruong();
//            $truong->setVotes(0);
//            $this->getDoctrine()->getManager()->persist($truong);
//            $this->getDoctrine()->getManager()->remove($pb);
//        }
//
//        $this->getDoctrine()->getManager()->flush();

        return new RedirectResponse($this->generateUrl('pin_test'));
    }

    /**
     * @Route("/", name="pin")
     */
    public function index()
    {
        return $this->render('pin/index.html.twig', [
            'controller_name' => 'PinController',
            'day' => '15',
            'test' => false
        ]);
    }

    /**
     * @Route("/test-choi", name="pin_test")
     */
    public function indexTest()
    {
        $now = new \DateTime();
        return $this->render('pin/index.html.twig', [
            'controller_name' => 'PinController',
            'day' => $now->format('d'),
            'test' => true
        ]);
    }


}
