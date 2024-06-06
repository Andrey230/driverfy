<?php

namespace App\Repository;

use App\Entity\Driver;
use App\Entity\MonthActivity;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MonthActivity>
 */
class MonthActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonthActivity::class);
    }

    //    /**
    //     * @return MonthActivity[] Returns an array of MonthActivity objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('m')
    //            ->andWhere('m.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('m.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    public function findByMonth(Driver $driver, string $month): ?MonthActivity
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.month = :val')
            ->andWhere('m.driver_id = :driver')
            ->setParameter('val', $month)
            ->setParameter('driver', $driver)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }

    public function findDriversByMonthAndUserId(string $month, string $id)
    {
        return $this->createQueryBuilder('ma')
            ->join('ma.driver_id', 'd')
            ->join('d.userId', 'u')
            ->andWhere('ma.month = :month')
            ->andWhere('u.id = :userId')
            ->setParameter('month', $month)
            ->setParameter('userId', $id)
            ->orderBy('ma.total_points', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
