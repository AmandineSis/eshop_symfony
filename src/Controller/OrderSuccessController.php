<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Classes\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/order/confirmation/{stripeSessionId}.html', name: 'order_confirmation')]
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneBystripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getStatus() == 0) {
            //vider la session "Cart"
            $cart->remove();
            //modifier le status à 1 - '
            $order->setStatus(1);
            $this->entityManager->flush();

            //Envoyer un Email à notre client pour lui confirmer sa commande
            //Envoi d'un email de confirmation
            $email = new Mail();
            $content = "Bonjour" . $order->getUser()->getFirstName() . "<br/>Merci pour votre commande " . $order->getReference() . "<br/><br/>Lorem, ipsum dolor sit amet consectetur adipisicing elit. Quo nam eveniet commodi. Earum, distinctio voluptatum! Earum rerum, eligendi harum, itaque maxime accusantium laudantium suscipit magnam, voluptatum modi dolor dolores hic?";
            $email->send($order->getUser()->getEmail(), $order->getUser()->getFirstName(), 'Votre commande sur Cocorico est bien validée', $content);
        }

        return $this->render('order_confirmation/success.html.twig', [
            //afficher les qqs infos de la commande de l'utilisateur
            'order' => $order
        ]);
    }
}
