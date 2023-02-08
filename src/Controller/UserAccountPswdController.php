<?php

namespace App\Controller;


use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAccountPswdController extends AbstractController
{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/account/password', name: 'account_password')]

    public function index(Request $req, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;
        
        $user = $this->getUser();
        //dd($user);
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($req); //écoute la requête entrante

        if ($form->isSubmitted() && $form->isValid()) {


            $oldPwd = $form->get('old_password')->getData();
            if ($encoder->isPasswordValid($user, $oldPwd)) {
                $newPwd = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $newPwd);

                $user->setPassword($password);
                $this->entityManager->flush(); //envoyer les données vers bdd

                $notification = "Votre mot de passe à bien été mis à jour";
            } else {
                $notification = "Votre mot de passe actuel est erroné, veuillez recommencer.";
            }
        }

        return $this->render('user_account/password.html.twig', [
            'passwordForm' => $form->createView(), //ajoute le formulaire à la vue
            'notification' => $notification
        ]);
    }
}
