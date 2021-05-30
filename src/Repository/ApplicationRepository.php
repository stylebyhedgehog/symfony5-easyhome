<?php

namespace App\Repository;

use App\Entity\ApplicationDTO;
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
     * @param ApplicationDTO $applicationDTO
     * @return Application[]
     */
    public function findByFiltersAndAccessType(ApplicationDTO $applicationDTO, int $id_user, string $acces, ?int $id_ad = null)
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

        if (!empty($applicationDTO->choice_status) and $applicationDTO->choice_status != 7) {
            $query = $query
                ->andWhere('a.status = :status')
                ->setParameter('status', $applicationDTO->choice_status);
        }
        if (!empty($applicationDTO->sort_param)) {

            if ($applicationDTO->sort_param == ApplicationFilter::$new) {
                $query = $query
                    ->orderBy('a.create_date', 'DESC');
            } elseif ($applicationDTO->sort_param == ApplicationFilter::$old) {
                $query = $query
                    ->orderBy('a.create_date', 'ASC');
            } else {
                return null;
            }
        }
        return $query->getQuery()->getResult();
    }

}
