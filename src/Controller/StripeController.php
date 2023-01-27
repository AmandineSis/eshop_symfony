<?php

namespace App\Controller;

use App\Classes\Cart;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class StripeController extends AbstractController
{


    #[Route('/order/checkout-session', name: 'stripe_checkout', methods: ['POST'])]
    public function createCharge(Cart $cart): Response
    {
        //Initialisation de la clé API
        Stripe::setApiKey('sk_test_51MSNTJJOUd0HUzDNzD55GQvEcSRgSW1nIPLGXJFtQLO4vwEV42mltZNtKVB6fMIw8kR2wTpRtcEbD66kjGjv3fgJ00Cwe4l20L');

        //Ajout du domaine utilisé
        $YOUR_DOMAIN = 'https://127.0.0.1:8000/';

        //Tableau regroupant tous les produits ajoutés au panier
        $product_for_stripe = [];
        //Ajout de chaque produits au tableau $product_for_stripe
        foreach ($cart->getFullCart() as $cartProduct) {
            $product_for_stripe[] =
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => $cartProduct['product']->getPrice(),
                        'product_data' => [
                            'name' => $cartProduct['product']->getName(),
                            'images' => [$YOUR_DOMAIN . "upload/" . $cartProduct['product']->getImage()]
                        ],
                    ],
                    'quantity' => $cartProduct['quantity']
                ];
        }

        //Création de la session de paiement
        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . 'success.html',
            'cancel_url' => $YOUR_DOMAIN . 'cancel.html',
        ]);
       

        return $this->redirect($checkout_session->url);
    }
}
