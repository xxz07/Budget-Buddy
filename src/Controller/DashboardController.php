<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;

final class DashboardController extends AbstractController
{
    public function __construct(private Security $security) {}

    #[Route("/dashboard", name: "app_dashboard")]
    public function index(): Response
    {
        $user = $this->security->getUser();

        if ($user != null) {
            return $this->render("dashboard/index.html.twig", []);
        }
        return $this->redirectToRoute("app_register");
    }
}
