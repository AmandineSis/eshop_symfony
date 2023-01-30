<?php

namespace App\Controller;

use App\Classes\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAccountAddressController extends AbstractController

{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }


    #[Route('/account/address', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('user_account/address.html.twig');
    }


    #[Route('/account/add_address', name: 'account_add_address')]
    public function add(Request $req, Cart $cart): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser()); //associe l'utilisateur aux données de l'adresse

            $this->entityManager->persist($address); //figer les données
            $this->entityManager->flush(); //envoyer les données vers bdd

            if ($cart->get()) {
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_address');
            }
        }

        return $this->render('user_account/address_form.html.twig', [
            'address_form' => $form->createView()
        ]);
    }


    #[Route('/account/edit_address/{id}', name: 'account_edit_address')]
    public function edit(Request $req, $id): Response
    {
        //recup avec doctrine (entityManager) dans le repo address de l'adresse correspondante à l'id
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if (!$address || $address->getUser() != $this->getUser()) { //si l'adresse n'existe pas ou si l'utilisateur associé à l'adresse est différent de l'utilisateur connecté
            return $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush(); //envoyer les données vers bdd
            return $this->redirectToRoute('account_address');
        }



        return $this->render('user_account/address_form.html.twig', [
            'address_form' => $form->createView()
        ]);
    }


    #[Route('/account/delete_address/{id}', name: 'account_delete_address')]
    public function delete($id): Response
    {
        //recup avec doctrine (entityManager) dans le repo address de l'adresse correspondante à l'id
        $address = $this->entityManager->getRepository(Address::class)->findOneById($id);

        if ($address && $address->getUser() == $this->getUser()) { //si l'adresse n'existe pas ou si l'utilisateur associé à l'adresse est différent de l'utilisateur connecté
            $this->entityManager->remove($address); //envoyer les données vers bdd
            $this->entityManager->flush(); //envoyer les données vers bdd
        }

        return $this->redirectToRoute('account_address');
    }
}
