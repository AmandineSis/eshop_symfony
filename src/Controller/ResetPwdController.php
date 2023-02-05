<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Entity\ResetPwd;
use App\Entity\User;
use App\Form\ResetPwdType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPwdController extends AbstractController
{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/reset-password', name: 'reset_pwd')]
    public function index(Request $request): Response
    {
        //Si User déjà connecté -> redirection vers Home
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //Recherche dans BDD si email existe bien
        if ($request->get('email')) {
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if ($user) {

                //1 : Enregistrer en base la demande de resetPwd avec user, token, createdAt
                $resetPwd = new ResetPwd();
                $resetPwd->setUser($user);
                $resetPwd->setToken(uniqid());
                $resetPwd->setCreatedAt(new DateTime());

                $this->entityManager->persist($resetPwd);
                $this->entityManager->flush();

                //2: Envoyer un email à l'utilisateur lui permettant de mettre à jour son mot de passe
                $url = $this->generateUrl('update_pwd', [
                    'token' => $resetPwd->getToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                $content = "Bonjour" . $user->getFirstName() . ",<br/>Vous avez demandé à réinitialiser votre mot de passe sur le site Cocorico.<br/><br/>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='" . $url . "'>mettre à jour votre mot de passe</a>.";

                $email = new Mail();
                $email->send(
                    $user->getEmail(),
                    $user->getFirstname() . ' ' . $user->getLastname(),
                    'Réinitialiser votre mot de passe sur Cocorico',
                    $content
                );
                $this->addFlash('notice', 'Un email a été envoyé à votre adresse email pour réinitialiser votre mot de passe.');
            } else {
                $this->addFlash('notice', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_pwd/index.html.twig');
    }

    #[Route('/update-password/{token}', name: 'update_pwd')]
    public function update($token, Request $req,  UserPasswordHasherInterface $encoder): Response
    {
        $resetPwd = $this->entityManager->getRepository(ResetPwd::class)->findOneByToken($token);


        if (!$resetPwd) {
            return $this->redirectToRoute('reset_pwd');
        }

        //Vérifier si createdAt == now - 3h

        $now = new DateTime();

        if ($now > $resetPwd->getCreatedAt()->modify('+ 3 hour')) {
            //token expiré
            $this->addFlash('notice', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_pwd');
        }

        //Rendre une vue avec mot de passe et confirmez votre mot de passe

        $form = $this->createForm(ResetPwdType::class);

        $form->handleRequest($req); //écoute la requête entrante
        if ($form->isSubmitted() && $form->isValid()) {
            $newPwd = $form->get('new_password')->getData();

            //encodage du mot de passe
            $password = $encoder->hashPassword($resetPwd->getUser(), $newPwd);
            $resetPwd->getUser()->setPassword($password);

            //flush en BDD
            $this->entityManager->flush();

            //redirection del'utilisateur vers lap age de connection
            $this->addFlash('notice', 'Votre mot de passe à bien été mis à jour.');
            return $this->redirectToRoute('login');
        }





        return $this->render('reset_pwd/update.html.twig', [
            'UpdatePwdForm' => $form->createView()
        ]);
    }
}
