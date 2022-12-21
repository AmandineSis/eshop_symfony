<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class SignupController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function index(): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);




        return $this->render('signup/index.html.twig', [
            'controller_name' => 'SignupController',
            'form' => $form,
        ]);
    }
}
