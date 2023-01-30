<?php

namespace App\Controller;

use App\Classes\Cart;
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

        if (!$order->IsPaid()) {
            //vider la session "Cart"
            $cart->remove();
            //modifier le status isPaid à 1 
            $order->setIsPaid(1);
            $this->entityManager->flush();

            //Envoyer un Email à notre client pour lui confirmer sa commande

        }

        return $this->render('order_confirmation/success.html.twig', [
            //afficher les qqs infos de la commande de l'utilisateur
            'order' => $order
        ]);
    }
}
