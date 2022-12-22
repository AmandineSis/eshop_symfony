<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SignupController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/signup', name: 'signup')]
    public function index(Request $req, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        $form = $form->handleRequest($req);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $user = $form->getData();
            $password = $passwordHasher->hashPassword($user, $user->getPassword());
            dd($password); //debbug
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }



        return $this->render('signup/index.html.twig', [
            'controller_name' => 'SignupController',
            'form' => $form,
        ]);
    }
}
