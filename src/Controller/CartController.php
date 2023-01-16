<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function PHPUnit\Framework\isEmpty;

class CartController extends AbstractController
{

    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/cart', name: 'cart')]
    public function index(Cart $cart): Response
    {

        $cartComplete = [];

        foreach ($cart->get() as $id => $quantity) {
            $cartComplete[] = [
                'product' => $this->entityManager->getRepository(Product::class)->findOneById($id),
                'quantity' => $quantity
            ];
        }

        if (count($cartComplete) > 0) {
            return $this->render('cart/index.html.twig', [
                'cart' => $cartComplete,
            ]);
        } else {
            return $this->redirectToRoute('products');
        };
    }
    #[Route('/cart/add/{id}', name: 'cart_add')]
    public function add(Cart $cart, $id): Response
    {

        $cart->add($id);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'cart_remove')]
    public function remove(Cart $cart): Response
    {

        $cart->remove();

        return $this->redirectToRoute('products');
    }

    #[Route('/cart/delete/{id}', name: 'delete_product')]
    public function deleteProduct(Cart $cart, $id): Response
    {

        $cart->delete($id);
        return $this->redirectToRoute('cart');
    }
}
