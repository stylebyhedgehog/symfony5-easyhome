<?php

namespace App\Repository;

use App\Data\AdData;
use App\Entity\Ad;
use App\Service\constants\AdFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ad|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ad|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ad[]    findAll()
 * @method Ad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ad::class);
    }
    public function save(Ad $ad){
        $em=$this->getEntityManager();
        $em->persist($ad);
        $em->flush();
    }

    /**
     * @param AdData $adData
     * @return Ad[]
     */
    public function findByFilters(AdData $adData){
        $query = $this
            ->createQueryBuilder('a')
            ->orderBy('a.update_date', 'DESC');

        if(!empty($adData->q)){
            $query= $query
                ->andWhere('a.city LIKE :q')
                ->setParameter('q', "%{$adData->q}%");
        }
        if(!empty($adData->min_price)){
            $query= $query
                ->andWhere('a.price > :min_price')
                ->setParameter('min_price', $adData->min_price);

        }
        if(!empty($adData->max_price)){
            $query= $query
                ->andWhere('a.price < :max_price')
                ->setParameter('max_price', $adData->max_price);

             }
        if(!empty($adData->min_sqr)){
            $query= $query
                ->andWhere('a.sqr > :min_sqr')
                ->setParameter('min_sqr', $adData->min_sqr);

        }
        if(!empty($adData->max_sqr)){
            $query= $query
                ->andWhere('a.sqr <= :max_sqr')
                ->setParameter('max_sqr', $adData->max_sqr);
        }
        if(!empty($adData->sort_param)){

            if($adData->sort_param == AdFilter::$new){
                $query= $query
                    ->orderBy('a.update_date', 'DESC');
            }
            elseif($adData->sort_param == AdFilter::$old){
                $query= $query
                    ->orderBy('a.update_date', 'ASC');
            }
            elseif ($adData->sort_param == AdFilter::$expensive){
                $query= $query
                    ->orderBy('a.price', 'DESC');
            }
            elseif ($adData->sort_param == AdFilter::$cheap){
                $query= $query
                    ->orderBy('a.price', 'ASC');
            }
            else{
                return null;
            }
        }

        return $query->getQuery()->getResult();

    }

    // /**
    //  * @return Ad[] Returns an array of Ad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ad
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
