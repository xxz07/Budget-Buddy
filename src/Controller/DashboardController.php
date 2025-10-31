<?php

namespace App\Controller;

use App\Entity\Transactions;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
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
    public function index(ChartBuilderInterface $chartBuilder, UserRepository $UserRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->security->getUser();
        
        

        // if the user is not signed in then return the user to the regrister page.
        if ($user === null) {
            return $this->redirectToRoute("app_register");
        }

        $userId = $user->getId();
        $transactions = $entityManager->getRepository(Transactions::class)->findAllTransactionsByUserId($userId);
        $income = $entityManager->getRepository(Transactions::class)->findAllIncomeByUserId($userId);

        // pre define chart data
        $incomeChartData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $transactionsChartData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];


        // UNCOMMENT THIS LATER OUT WHEN WORKING ON PROJECT
        // get transactions and put them in data
        // $month = 1;
        // $amount = 0;
        // $currentYear = date("Y");
        // foreach ($transactions as $value) {
        //     $monthValue = date('m', strtotime($value["date"]));
        //     $yearValue = date('Y', strtotime($value["date"]));

        //     if ($yearValue == $currentYear) {
        //         if ($month != $monthValue) {
        //             $incomeChartData[$month + 0] += $amount;
        //             $amount = 0;
        //             $month = $monthValue;
        //         }
        //         $amount += $value["amount"];
        //     }
            
            
        // }


        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            'datasets' => [
                [
                    'label' => 'Income',
                    'backgroundColor' => 'rgba(206, 38, 75, 1)',
                    'borderColor' => 'rgba(206, 38, 75, 1)',
                    'data' => $incomeChartData,
                ],
                [
                    'label' => 'Transactions',
                    'backgroundColor' => 'rgba(34, 239, 75, 1)',
                    'borderColor' => 'rgba(34, 239, 75, 1)',
                    'data' => [0, 0, 0, 0, 0, 0, 0],
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


        
        return $this->render("dashboard/index.html.twig", [
            "user" => $userId,
            'chart' => $chart,
        ]);
    }
}
