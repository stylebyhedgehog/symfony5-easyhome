<?php


namespace App\Service;


use App\DTO\RecommendationDTO;
use App\Entity\Ad;
use App\Entity\BrowsingHistory;
use App\Entity\Client;
use App\Repository\AdRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class AdRecommendationService
{
public function push(Ad $ad, Client $client,EntityManagerInterface $entityManager){
    if (!in_array($ad,$client->getBrowsingHistoryAd()->toArray()) and $ad->getOwner()->getId()!=$client->getId()){
        $browsingHistory=new BrowsingHistory;
        $browsingHistory->setAd($ad);
        $browsingHistory->setClient($client);
        $browsingHistory->setCreateDate(new DateTime());
        $entityManager->persist($browsingHistory);
        $entityManager->flush();

    }
}
public function getRecommendation(Client $client){
    $watched_ads= $client->getBrowsingHistoryAd()->toArray();
    $district=$this->max_attribute_in_array($watched_ads,'getDistrict');
    $city=$this->max_attribute_in_array($watched_ads,'getCity');
    $recommendation=new RecommendationDTO();
    $recommendation->district=$district;
    $recommendation->city=$city;
    return $recommendation;
}
   private function max_attribute_in_array($array, $prop) {
        return max(array_map(function($o) use($prop) {
            return $o->$prop();
        },
            $array));
    }
}
