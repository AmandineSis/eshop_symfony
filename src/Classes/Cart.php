<?php

namespace App\Classes;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;

class Cart extends AbstractController
{
    protected $requestStack;
    private $entityManager; //convention de nommage pour doctrine


    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    /**
     * 
     */
    public function add($id) //add($id) provient du controller cart
    {
        $session = $this->requestStack->getSession();
        // stores an attribute for reuse during a later user request $session->set('attribute-name', 'attribute-value');
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

    public function delete($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        unset($cart[$id]);
        $session->set('cart', $cart);
        return $session->get('cart');
    }

    public function decreaseQuantity($id)
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        };

        $session->set('cart', $cart);
    }

    public function getFullCart()
    {
        $session = $this->requestStack->getSession();
        $cart = $session->get('cart');
        $cartComplete = [];

        foreach ($this->get() as $id => $quantity) {
            $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);

            //si le produit retourné par l'Id n'existe pas alors suppression du produit
            if (!$product_object) {
                $this->delete($id);
                continue; //sort de la boucle forEach et donc n'affecte pas de produit à la variable $cartComplete
            }
            $cartComplete[] = [
                'product' => $product_object,
                'quantity' => $quantity
            ];
        }    
        

        return $cartComplete;
    }
};
