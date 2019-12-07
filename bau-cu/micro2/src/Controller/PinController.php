<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        return $this->render('pin/vong-1.html.twig', [
            'controller_name' => 'PinController',
        ]);
    }
}
