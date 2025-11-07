<?php

namespace App\Repository;

use App\Entity\Transactions;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transactions>
 */
class TransactionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transactions::class);
    }

//    /**
//     * @return Transactions[] Returns an array of Transactions objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Transactions
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllTransactionsByUserId(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // type is 1 because its transactions
        $sql = 'SELECT * FROM `transactions` WHERE `user_id` = :id AND `type` = 1 ORDER BY date ASC;';

        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findAllIncomeByUserId(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // type is 0 because its Income
        $sql = 'SELECT * FROM `transactions` WHERE `user_id` = :id AND `type` = 0 ORDER BY date ASC;';

        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findLatestActivity(int $id): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // type is 0 because its Income
        $sql = 'SELECT * FROM `transactions` WHERE `user_id` = :id ORDER BY date DESC LIMIT 6;';

        $resultSet = $conn->executeQuery($sql, ['id' => $id]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAllAssociative();
    }

    public function findActivityById(int $id, int $userId): array
    {
        $conn = $this->getEntityManager()->getConnection();

        // type is 0 because its Income
        $sql = 'SELECT * FROM `transactions` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1;';

        $resultSet = $conn->executeQuery($sql, ['id' => $id, 'user_id' => $userId]);

        // returns an array of arrays (i.e. a raw data set)
        return $resultSet->fetchAssociative();
    }
}
