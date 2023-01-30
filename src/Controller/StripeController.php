<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StripeController extends AbstractController
{


    #[Route('/order/checkout-session/{reference}', name: 'stripe_checkout', methods: ['POST'])]
    public function createCharge(EntityManagerInterface $entityManager, Cart $cart, $reference): Response
    {
        //Initialisation de la clé API
        Stripe::setApiKey('sk_test_51MSNTJJOUd0HUzDNzD55GQvEcSRgSW1nIPLGXJFtQLO4vwEV42mltZNtKVB6fMIw8kR2wTpRtcEbD66kjGjv3fgJ00Cwe4l20L');

        //Ajout du domaine utilisé
        $YOUR_DOMAIN = 'https://127.0.0.1:8000/';

        //Récupération de la référence de commande
        $order = $entityManager->getRepository(Order::class)->findOneByReference($reference);
        $orderDetails = $order->getOrderDetails()->getValues();

        //Tableau regroupant tous les produits ajoutés au panier
        $product_for_stripe = [];
        //Ajout de chaque produits au tableau $product_for_stripe
        foreach ($orderDetails as $cartProduct) {
            $product_object = $entityManager->getRepository(Product::class)->findOneByName($cartProduct->getProduct());

            $product_for_stripe[] =
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $cartProduct->getPrice(),
                        'product_data' => [
                            'name' => $product_object->getName(),
                            'images' => [$YOUR_DOMAIN . "upload/" . $product_object->getImage()]
                        ],
                    ],
                    'quantity' => $cartProduct->getQuantity()
                ];
        }
        //Ajout du transporteur
        $product_for_stripe[] =
            [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $order->getCarrierPrice(),
                    'product_data' => [
                        'name' => $order->getCarrierName(),
                        'images' => [$YOUR_DOMAIN] //A modifier avec logo transporteur
                    ],
                ],
                'quantity' => 1,
            ];


        //Création de la session de paiement
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . 'order/confirmation/{CHECKOUT_SESSION_ID}.html',
            'cancel_url' => $YOUR_DOMAIN . 'order/error/{CHECKOUT_SESSION_ID}.html',
        ]);
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();
        return $this->redirect($checkout_session->url);
    }
}
