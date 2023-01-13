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

        $session->set('cart', [
            [
                'id' => $id,
                'quantity' => 1
            ]
        ]);
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
