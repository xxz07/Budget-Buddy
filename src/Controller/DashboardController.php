<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Form\UserTransactionsType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Component\HttpFoundation\Request;
use Symfony\UX\Chartjs\Model\Chart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;

final class DashboardController extends AbstractController
{
    public function __construct(private Security $security) {}

    #[Route("/dashboard", name: "app_dashboard")]
    public function index(Request $request, ChartBuilderInterface $chartBuilder, UserRepository $UserRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();
        
        

        // if the user is not signed in then return the user to the register page.
        if ($user === null) {
            return $this->redirectToRoute("app_register");
        }

        $userId = $user->getId();
        $transactions = $entityManager->getRepository(Transactions::class)->findAllTransactionsByUserId($userId);
        $income = $entityManager->getRepository(Transactions::class)->findAllIncomeByUserId($userId);
        $activity = $entityManager->getRepository(Transactions::class)->findLatestActivity($userId);

        // pre define chart data
        $incomeChartData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $transactionsChartData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        // get transactions data and put them in a chart
        $currentYear = date("Y");
        foreach ($transactions as $value) {
            $monthValue = (int)date('m', strtotime($value["date"]));
            $yearValue = (int)date('Y', strtotime($value["date"]));

            if ($yearValue == $currentYear) {
                $transactionsChartData[$monthValue - 1] += $value["amount"];
            }
        }

        // get income data and put them in a chart
        $currentYear = date("Y");
        foreach ($income as $value) {
            $monthValue = (int)date('m', strtotime($value["date"]));
            $yearValue = (int)date('Y', strtotime($value["date"]));

            if ($yearValue == $currentYear) {
                $incomeChartData[$monthValue - 1] += $value["amount"];
            }
        }


        // generate the form
        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'backgroundColor' => 'rgba(34, 239, 75, 1)',
                    'borderColor' => 'rgba(34, 239, 75, 1)',
                    'data' => $incomeChartData,
                ],
                [
                    'label' => 'Expense',
                    'backgroundColor' => 'rgba(206, 38, 75, 1)',
                    'borderColor' => 'rgba(206, 38, 75, 1)',
                    'data' => $transactionsChartData,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                ],
            ],
        ]);
        
        // Create form
        $transactionsForm = new Transactions();
        $form=$this -> createForm(UserTransactionsType::class, $transactionsForm);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $transactionsForm = $form->getData();
            $transactionsForm->setUser($user);
            $entityManager->persist($transactionsForm);
            $entityManager->flush();
        }
        
        return $this->render("dashboard/index.html.twig", [
            'user' => $userId,
            'chart' => $chart,
            'form' => $form,
            'activities' => $activity
            // "transactions" => $transactions,
            // "income" => $income
        ]);
    }


    //TODO: add full settings here, this is a burner
    #[Route("/settings", name: "app_settings")]
    public function settings(ChartBuilderInterface $chartBuilder, UserRepository $UserRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();
        
        

        // if the user is not signed in then return the user to the regrister page.
        if ($user === null) {
            return $this->redirectToRoute("app_register");
        }

        $userId = $user->getId();
        
        return $this->render("dashboard/settings.html.twig", [
            "user" => $userId,

        ]);
    }
}
