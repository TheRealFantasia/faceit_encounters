<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="vue")
     */
    public function index()
    {
        /** @var User $user */
        $user = $this->getUser();
        return $this->render('index/index.html.twig', [
            'user' => $user !== null ? $user->toArray() : []
        ]);
    }
}