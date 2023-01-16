<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountAddressController extends AbstractController
{
    #[Route('/user/account/address', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('user_account/address.html.twig');
    }

    #[Route('/user/account/add_address', name: 'account_add_address')]
    public function add(): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        return $this->render('user_account/add_address.html.twig', [
            'address_form' => $form->createView()
        ]);
    }
}
