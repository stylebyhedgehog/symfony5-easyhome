<?php

namespace App\Repository;

use App\Entity\BrowsingHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BrowsingHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method BrowsingHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method BrowsingHistory[]    findAll()
 * @method BrowsingHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BrowsingHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BrowsingHistory::class);
    }

    // /**
    //  * @return BrowsingHistory[] Returns an array of BrowsingHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BrowsingHistory
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
