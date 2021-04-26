<?php

namespace App\Repository;

use App\Data\ApplicationData;
use App\Entity\Application;
use App\Service\constants\ApplicationFilter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Application|null find($id, $lockMode = null, $lockVersion = null)
 * @method Application|null findOneBy(array $criteria, array $orderBy = null)
 * @method Application[]    findAll()
 * @method Application[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ApplicationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Application::class);
    }

    /**
     * @param ApplicationData $applicationData
     * @return Application[]
     */
    public function findByFiltersAndAccessType(ApplicationData $applicationData, int $id_user, string $acces, ?int $id_ad = null)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.' . $acces . '= :id_user')
            ->setParameter('id_user', $id_user);
        if ($id_ad != null) {
            $query = $query
                ->andWhere('a.ad = :id_ad')
                ->setParameter('id_ad', $id_ad);
        }
        $query = $query
            ->orderBy('a.create_date', 'DESC');

        if (!empty($applicationData->choice_status) and $applicationData->choice_status != 7) {
            $query = $query
                ->andWhere('a.status = :status')
                ->setParameter('status', $applicationData->choice_status);
        }
        if (!empty($applicationData->sort_param)) {

            if ($applicationData->sort_param == ApplicationFilter::$new) {
                $query = $query
                    ->orderBy('a.create_date', 'DESC');
            } elseif ($applicationData->sort_param == ApplicationFilter::$old) {
                $query = $query
                    ->orderBy('a.create_date', 'ASC');
            } else {
                return null;
            }
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Application[] Returns an array of Application objects
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
    public function findOneBySomeField($value): ?Application
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
