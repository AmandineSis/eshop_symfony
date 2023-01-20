<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
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

        return $this->render('order/add.html.twig', [
            'order_form' => $form->createView(),
            'cart' => $cart->getFullCart()
        ]);
    }

    #[Route('/order/summary', name: 'order_summary')]
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
            dd($delivery_content);
            //enregistrer ma commande Order()
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carrier->getName());
            $order->setCarrierprice($carrier->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            //enregistrer mes produits OrderDetails()
        }

        return $this->render('order/index.html.twig', [
            'cart' => $cart->getFullCart()
        ]);
    }
}
