<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/order', name: 'order')]
    public function index(Cart $cart): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('account_add_address');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser() //permet de récup du coté formulaire en option l'utilisateur en cours
        ]);

        return $this->render('order/index.html.twig', [
            'order_form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }

    #[Route('/order/summary', name: 'order_summary', methods: ['POST'])]
    public function addOrder(Cart $cart, Request $req): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser() //permet de récup du coté formulaire en option l'utilisateur en cours
        ]);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {

            //Mise en forme de l'adresse de livraison
            $date = new DateTime();
            $carrier = $form->get('carrier')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            $delivery_content .= '<br/>' . $delivery->getPhone();

            if ($delivery->getCompany()) {
                $delivery_content .= '<br/>' . $delivery->getCompany();
            };

            $delivery_content .= '<br/>' . $delivery->getAddress();
            $delivery_content .= '<br/>' . $delivery->getPostcode() . ' ' . $delivery->getCity();
            $delivery_content .= '<br/>' . $delivery->getCountry();

            //enregistrer ma commande Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierprice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            //Ajout des données dans la table order
            $this->entityManager->persist($order);
            //enregistrer mes produits OrderDetails()

            $product_for_stripe = [];
            $YOUR_DOMAIN = 'https://127.0.0.1:8000/';
            //Pour chauqe produits du panier je veux que tu fasses une nouvelle entrée dans order details et enfin que tu fasses le lien entre orderDetails() et order()

            foreach ($cart->getFullCart() as $cartProduct) {
                $orderDetails = new OrderDetails();

                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($cartProduct['product']->getName());
                $orderDetails->setQuantity($cartProduct['quantity']); //tableau quantity se trouve directement dans l'objet $cartProduct
                $orderDetails->setPrice($cartProduct['product']->getPrice());
                $orderDetails->setTotal($cartProduct['product']->getPrice() * $cartProduct['quantity']);
                //ajout des données dansla table orderDetails
                $this->entityManager->persist($orderDetails);

                $product_for_stripe[] = 
                    // 'price_data' => [
                    //     'price' => 'price_1MTmadJOUd0HUzDNp1H6v6Zw',
                    //     'currency' => 'eur',
                    //     'unit_amount' => $cartProduct['product']->getPrice(),
                    //     'product_data' => [
                    //         'name' => $cartProduct['product']->getName(),
                    //         'images' => [$YOUR_DOMAIN . "upload/" . $cartProduct['product']->getImage()],
                    //     ],
                    // ],
                    // 'quantity' => $cartProduct['quantity'],
                    [
                        'price_data' => [
                            'currency' => 'eur',
                            'unit_amount' => $cartProduct['product']->getPrice(),
                            'product_data' => [
                                'name' => $cartProduct['product']->getName(),
                                'images' => [$YOUR_DOMAIN . "upload/" . $cartProduct['product']->getImage()]
                            ],
                        ],
                        'quantity' => $cartProduct['quantity'],
                    
                ];
            }
            //dd($product_for_stripe);
            /********************************************************************* */
            Stripe::setApiKey('sk_test_51MSNTJJOUd0HUzDNzD55GQvEcSRgSW1nIPLGXJFtQLO4vwEV42mltZNtKVB6fMIw8kR2wTpRtcEbD66kjGjv3fgJ00Cwe4l20L');

            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => $YOUR_DOMAIN . '/success.html',
                'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
            ]);

            dd($checkout_session);








            /********************************************************************** */
            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFullCart(),
                'carrier' => $carrier,
                'delivery' => $delivery_content
            ]);
        }

        return $this->redirectToRoute('cart');
    }
}
