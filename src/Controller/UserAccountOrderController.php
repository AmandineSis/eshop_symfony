<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountOrderController extends AbstractController
{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }

    #[Route('/account/order', name: 'account_order')]
    public function index(): Response
    {
        $orders = $this->entityManager->getRepository(Order::class)->findSuccessOrders($this->getUser());
        
        return $this->render('user_account/order.html.twig', [
            'orders' => $orders
        ]);
    }
}
