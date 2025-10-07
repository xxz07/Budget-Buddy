<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GeneralController extends AbstractController
{
    #[Route('/', name: 'app_general')]
    public function index(): Response
    {
        return $this->render('general/index.html.twig', [
            'controller_name' => 'GeneralController',
        ]);
    }
    #[Route('/about-us', name: 'app_about_us')]
    public function about(): Response
    {
        return $this->render('general/about-us.html.twig', [
            'controller_name' => 'GeneralController',
        ]);
    }
}
