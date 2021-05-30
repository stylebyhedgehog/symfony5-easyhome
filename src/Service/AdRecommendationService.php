<?php


namespace App\Service;


use App\Entity\RecommendationDTO;
use App\Entity\Ad;
use App\Entity\BrowsingHistory;
use App\Entity\Client;
use App\Repository\AdRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AdRecommendationService
{
    private $entityManager;

    /**
     * AdRecommendationService constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function push(Ad $ad, UserInterface $client)
    {
        if (!in_array($ad, $client->getBrowsingHistoryAd()->toArray()) and $ad->getOwner()->getId() != $client->getId()) {
            $browsingHistory = new BrowsingHistory;
            $browsingHistory->setAd($ad);
            $browsingHistory->setClient($client);
            $browsingHistory->setCreateDate(new DateTime());
            $this->entityManager->persist($browsingHistory);
            $this->entityManager->flush();

        }
    }

    public function getRecommendation(UserInterface $client)
    {
        $watched_ads = $client->getBrowsingHistoryAd()->toArray();
        $recommendation = new RecommendationDTO();
        if (empty($watched_ads)){return $recommendation;}
        else{
        $district = $this->max_attribute_in_array($watched_ads, 'getDistrict');
        $city = $this->max_attribute_in_array($watched_ads, 'getCity');
        $recommendation->district = $district;
        $recommendation->city = $city;
        return $recommendation;}
    }
    private function max_attribute_in_array($array, $prop)
    {
        $arr_with_total=(array_map(function ($o) use ($prop) {
            if($o->$prop()!=null){
                return $o->$prop();
            }
            else {return '';}
        },
            $array));
        $arr_with_max_count=array_count_values($arr_with_total);
        $maxVal = max($arr_with_max_count);
        $maxKey = array_search($maxVal, $arr_with_max_count);
        return $maxKey;
    }
}
