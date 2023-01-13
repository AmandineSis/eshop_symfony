<?php

namespace App\Controller;

use App\Classes\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{

    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {
        dd($cart->get());
         return $this->render('cart/index.html.twig');
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(Cart $cart, $id): Response
    {
       
        $cart->add($id);

        return $this->redirectToRoute('cart');
    }
}