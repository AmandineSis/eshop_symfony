<?php

namespace App\Controller;

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

    #[Route('/user/account/address', name: 'account_address')]
    public function index(): Response
    {
        return $this->render('user_account/address.html.twig');
    }

    #[Route('/user/account/add_address', name: 'account_add_address')]
    public function add(Request $req): Response
    {
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $address->setUser($this->getUser()); //ajoute aux données de l'adresse le nom de l'utilisateur

            $this->entityManager->persist($address); //figer les données
            $this->entityManager->flush(); //envoyer les données vers bdd
            return $this->redirectToRoute('account_address');
        }



        return $this->render('user_account/add_address.html.twig', [
            'address_form' => $form->createView()
        ]);
    }
}
