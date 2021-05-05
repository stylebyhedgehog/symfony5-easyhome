<?php

namespace App\Repository;

use App\Entity\AdImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AdImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdImage[]    findAll()
 * @method AdImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdImage::class);
    }

}
