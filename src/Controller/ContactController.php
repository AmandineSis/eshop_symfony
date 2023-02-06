<?php

namespace App\Controller;

use App\Classes\Mail;
use App\Form\ContactType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request as HttpFoundationRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function index(Request $req): Response
    {
        $form =  $this->createForm(ContactType::class);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('notice', 'Merci de nous avoir contacté, Notre équipe vous répondra dans les meilleurs délais.');
            //Gestion des messages utilisateurs

            // $mail = new Mail();
            // $mail->send('','Cocorico', 'Vous avez reçu un message', 'contenu du message' );
        }

        return $this->render('contact/index.html.twig', [
            'contactForm' => $form->createView()
        ]);
    }
}
