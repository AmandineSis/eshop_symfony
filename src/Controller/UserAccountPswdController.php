<?php

namespace App\Controller;


use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

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
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();
            dd($old_pwd);
        }

        return $this->render('user_account/password.html.twig', [
            'passwordForm' => $form->createView()
        ]);
    }
}
