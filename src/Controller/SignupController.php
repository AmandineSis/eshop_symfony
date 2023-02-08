<?php

namespace App\Controller;

use App\Classes\Mail;
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
        $notification = null;

        $user = new User();
        $form =  $this->createForm(SignupType::class, $user); //création du formulaire

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            //vérification utilisateur unique
            $serach_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

            if ($serach_email) {
                //Envoi d'une notification
                $notification = "L'email que vous avez renseigné existe déjà";
            } else {
                $password = $encoder->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $this->entityManager->persist($user); //figer les données
                $this->entityManager->flush(); //envoyer les données vers bdd

                //Envoi d'un email de confirmation
                $email = new Mail();
                $content = "Bonjour" . $user->getFirstName() . "<br/>Bienvenue sur la boutique made in France.<br/><br/>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo nam eveniet commodi. Earum, distinctio voluptatum! Earum rerum, eligendi harum, itaque maxime accusantium laudantium suscipit magnam, voluptatum modi dolor dolores hic?";
                $email->send($user->getEmail(), $user->getFirstName(), 'Bienvenue sur Cocorico', $content);

                //Notification de prise en compte de l'inscription
                $notification = "Votre inscription est prise en compte, vous pouvez dès à présent vous connecterà  votre compte";
            }
        }


        return $this->render('signup/index.html.twig', [
            'myForm' => $form->createView(), //crée la vue du formulaire
            'notification' => $notification
        ]);
    }
}
