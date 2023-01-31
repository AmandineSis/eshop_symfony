<?php

namespace App\Controller;

use App\Classes\Mail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $mail = new Mail();
        $mail->send('am.sismondi@gmail.com', 'Harry Potter', 'Mon premier mail', 'Bonjour, comment ça va ?');

        return $this->render('home/index.html.twig');
    }
}
