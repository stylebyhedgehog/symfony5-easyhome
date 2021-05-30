<?php

namespace App\Repository;

use App\Entity\AdDto;
use App\Entity\RecommendationDTO;
use App\Entity\Ad;
use App\Service\constants\AdFilter;
use App\Service\constants\AdStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
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

    public function findControlledAdsByFilters(AdDto $adDTO, int $id_user){
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.agent = :id_user')
            ->setParameter('id_user', $id_user)
            ->orderBy('a.update_date', 'DESC');
        return $this->filterStatusDate($query,$adDTO);
    }

    public function findPostedAdsByFilters(AdDto $adDTO, int $id_user){
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.owner = :id_user')
            ->setParameter('id_user', $id_user)
            ->orderBy('a.update_date', 'DESC');
        return $this->filterStatusDate($query,$adDTO);
    }

    /**
     * @param QueryBuilder $query
     * @param AdDto $adDTO
     * @return null
     */
    public function filterStatusDate(QueryBuilder $query, AdDto $adDTO)
    {
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

    public function findRecommendAdsByFilters(AdDto $adDTO, RecommendationDTO $recommendationDTO){
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', AdStatus::$status_ok)
            ->andWhere('a.city = :city')
            ->setParameter('city',$recommendationDTO->city)
            ->andWhere('a.district = :district')
            ->setParameter('district',$recommendationDTO->district)
            ->orderBy('a.update_date', 'DESC');
        return $this->filterGlobal($query,$adDTO);
    }

    public function findAllAdsByFilters(AdDto $adDTO){
        $query = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', AdStatus::$status_ok)
            ->orderBy('a.update_date', 'DESC');
        return $this->filterGlobal($query,$adDTO);
    }

    /**
     * @param QueryBuilder $query
     * @param AdDto $adDTO
     * @return Ad[]
     */
    public function filterGlobal(QueryBuilder $query, AdDto $adDTO)
    {
        if (!empty($adDTO->q)) {
            $query = $query
                ->andWhere('a.city LIKE :q')
                ->setParameter('q', $adDTO->q)
                ->orWhere('a.street LIKE :q')
                ->setParameter('q', $adDTO->q);
        }
        if (!empty($adDTO->city) and empty($adDTO->q) and $adDTO->city!='.Все') {
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
