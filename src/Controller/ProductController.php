<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private $entityManager; //convention de nommage pour doctrine

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager; //mets l'entityManager que l'on vient d'instancier dans la variable privée entityManager
    }


    #[Route('/products', name: 'products')]
    public function index(): Response
    {
        $products = $this->entityManager->getRepository(Product::class)->findAll();

        return $this->render('product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{slug}', name: 'product')]
    public function displayProduct($slug): Response
    {
        //  dd($slug); //test récupére la variable {slug} de l'url et l'injecte dans $slug

        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);

        //si produit n'est pas trouvé alors redirection vers la page des produits
        if (!$product) {
            return $this->redirectToRoute('products');
        }

        return $this->render('product/displayProduct.html.twig', [
            'product' => $product,
        ]);
    }
}
