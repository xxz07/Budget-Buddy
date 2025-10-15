<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Form\TransactionsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

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
    #[Route('/dashboard', name: 'app_about_us')]
    public function dashboard(EntityManagerInterface $entityManager): Response
    {
        $showTransactions = $entityManager->getRepository(Transactions::class)->findAll();

        $transactions = new Transactions();
        $form = $this->createForm(TransactionsType::class, $transactions);

        return $this->render('general/dashboard.html.twig', [
            'controller_name' => 'GeneralController',
            'form' => $form,
            'showTransactions' => $showTransactions,
        ]);
    }
}
