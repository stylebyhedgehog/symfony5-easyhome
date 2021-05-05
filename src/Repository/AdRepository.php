<?php

namespace App\Repository;

use App\Data\AdDTO;
use App\DTO\RecommendationDTO;
use App\Entity\Ad;
use App\Service\constants\AdFilter;
use App\Service\constants\AdStatus;
use App\Service\constants\Mode;
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

    public function save(Ad $ad)
    {
        $em = $this->getEntityManager();
        $em->persist($ad);
        $em->flush();
    }

    /**
     * @param AdDTO $adDTO
     * @param int $mode
     * @param int $id_user
     * @return Ad[]
     */
    public function findByUserFiltersWithMode(AdDTO $adDTO, int $mode, int $id_user)
    {
        $query = $this
            ->createQueryBuilder('a');
            if ($mode== Mode::$client_ad_posted){
                $query = $query
                    ->where('a.owner = :id_user')
                    ->setParameter('id_user', $id_user)
                    ->orderBy('a.update_date', 'DESC');
            }
            elseif ($mode == Mode::$agent_ad_controlled){
                $query = $query
                    ->where('a.agent = :id_user')
                    ->setParameter('id_user', $id_user)
                    ->orderBy('a.update_date', 'DESC');
            }

        if (!empty($adDTO->choice_status) and $adDTO->choice_status != 5) {
            $query = $query
                ->andWhere('a.status = :status')
                ->setParameter('status', $adDTO->choice_status);
        }
        if (!empty($adDTO->sort_param)) {

            if ($adDTO->sort_param == AdFilter::$new) {
                $query = $query
                    ->orderBy('a.update_date', 'DESC');
            } elseif ($adDTO->sort_param == AdFilter::$old) {
                $query = $query
                    ->orderBy('a.update_date', 'ASC');
            } else {
                return null;
            }
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param AdDTO $adDTO
     * @param int|null $mode
     * @param RecommendationDTO|null $recommendation
     * @return Ad[]
     */
    public function findByClientFilters(AdDTO $adDTO, ?int $mode, ?RecommendationDTO $recommendation=null)
    {
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', AdStatus::$status_ok);
            if ($mode == Mode::$recommendation_ads){
                $query = $query
                    ->andWhere('a.city = :city')
                    ->setParameter('city',"%{$recommendation->city}%")
                    ->andWhere('a.district = :district')
                    ->setParameter('district',"%{$recommendation->district}%");
            }
            $query=$query
            ->orderBy('a.update_date', 'DESC');

        if (!empty($adDTO->q)) {
            $query = $query
                ->andWhere('a.city LIKE :q')
                ->setParameter('q', "%{$adDTO->q}%")
                ->orWhere('a.street LIKE :q')
                ->setParameter('q', "%{$adDTO->q}%");
        }
        if (!empty($adDTO->city)) {
            $query = $query
                ->andWhere('a.city = :city')
                ->setParameter('city', $adDTO->city);

        }
        if (!empty($adDTO->min_price)) {
            $query = $query
                ->andWhere('a.price > :min_price')
                ->setParameter('min_price', $adDTO->min_price);

        }
        if (!empty($adDTO->max_price)) {
            $query = $query
                ->andWhere('a.price < :max_price')
                ->setParameter('max_price', $adDTO->max_price);

        }
        if (!empty($adDTO->min_sqr)) {
            $query = $query
                ->andWhere('a.sqr > :min_sqr')
                ->setParameter('min_sqr', $adDTO->min_sqr);

        }
        if (!empty($adDTO->max_sqr)) {
            $query = $query
                ->andWhere('a.sqr <= :max_sqr')
                ->setParameter('max_sqr', $adDTO->max_sqr);
        }
        if (!empty($adDTO->sort_param)) {

            if ($adDTO->sort_param == AdFilter::$new) {
                $query = $query
                    ->orderBy('a.update_date', 'DESC');
            } elseif ($adDTO->sort_param == AdFilter::$old) {
                $query = $query
                    ->orderBy('a.update_date', 'ASC');
            } elseif ($adDTO->sort_param == AdFilter::$expensive) {
                $query = $query
                    ->orderBy('a.price', 'DESC');
            } elseif ($adDTO->sort_param == AdFilter::$cheap) {
                $query = $query
                    ->orderBy('a.price', 'ASC');
            } else {
                return null;
            }
        }

        return $query->getQuery()->getResult();

    }

}
