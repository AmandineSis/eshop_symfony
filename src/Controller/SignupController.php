<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SignupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/signup', name: 'signup')]

    public function index(Request $req, UserPasswordHasherInterface $encoder): Response
    {
        $user = new User();
        $form =  $this->createForm(SignupType::class, $user); //création du formulaire
        
        $form->handleRequest($req);

        if($form->isSubmitted() && $form->isValid()){
            $user = $form->getData();

            $password = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->entityManager->persist($user);//figer les données
            $this->entityManager->flush();//envoyer les données vers bdd
        }


        return $this->render('signup/index.html.twig', [
            'myForm' => $form->createView(), //crée la vue du formulaire
            
        ]);
    }
}
