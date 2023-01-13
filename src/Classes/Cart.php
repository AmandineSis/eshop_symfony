<?php

namespace App\Classes;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart extends AbstractController
{
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * 
     */
    public function add($id) //add($id) provient du controller cart
    {
        $session = $this->requestStack->getSession();

        $cart = $session->get('cart', []);

        //si product.id existe dans le panier alors ajoute +1 à la quantité, sinon ajoute le produit au panier
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);
    }

    /**
     * 
     */
    public function get()
    {
        $session = $this->requestStack->getSession();
        return $session->get('cart');
    }

    /**
     * 
     */
    public function remove()
    {
        $session = $this->requestStack->getSession();
        return $session->remove('cart');
    }
};
