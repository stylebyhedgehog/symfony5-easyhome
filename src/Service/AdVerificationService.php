<?php


namespace App\Service;


use App\Entity\Ad;
use App\Repository\AdRepository;

class AdVerificationService
{

    private $adRepository;

    /**
     * AdVerificationService constructor.
     * @param AdRepository $adRepository
     */
    public function __construct(AdRepository $adRepository)
    {
        $this->adRepository = $adRepository;
    }

    function getDataByAddress(string $city,string $street_type,string $street,string $house,?string $flat){
        $token = "18146cd0fd1b5aa3fc838c2ab74c3e7e67ef4520";
        $secret = "6b724d29d61dceafaa93bf7f53b80fec8027616a";
        $dadata = new \Dadata\DadataClient($token, $secret);
        return $dadata->clean("address", $city.", ".$street_type." ".$street.", ".$house.", ".$flat);
    }
    function checkAddress(Ad $ad){
        if (!$this->uniqCheck($ad)){
            return $error="Объявление с данным адресом уже размещено";
        }
        $city=$ad->getCity();
        $street_type=$ad->getStreetType();
        $street=$ad->getStreet();
        $house=$ad->getHouseNumber();
        $flat=$ad->getFlatNumber();
        $result = $this->getDataByAddress($city,$street_type,$street,$house,$flat);
        if (!$this->isExist($result)){
            return $error="Введенного Вами адреса не существет";
        }
        if(!$this->isCorrect($ad,$result)){
            return $error="Некорректный адрес (Возможно Вы имели в виду: ".$result["result"].")";
        }
        return null;
    }
    function uniqCheck(Ad $ad_input){
        $ads=$this->adRepository->findAll();
        foreach ($ads as $ad){
            if ($ad->getCity()==$ad_input->getCity() and $ad->getStreetType()==$ad_input->getStreetType()
            and $ad->getStreet()==$ad_input->getStreet() and $ad->getHouseNumber()==$ad_input->getHouseNumber()
            and $ad->getFlatNumber()==$ad_input->getFlatNumber()){
                return false;
            }
        }
        return true;
    }


    function isExist(array $result){
        if(empty($result["city"]) or empty($result["street"]) or empty($result["house"])){
            return false;
        }
        return true;
    }
    function isCorrect(Ad $ad,array $result){
        if($ad->getCity()!=$result["city"] or $ad->getStreet()!=$result["street"]
            or $ad->getHouseNumber()!=$result["house"] or $ad->getFlatNumber()!=$result["flat"]){
            return false;
        }
        return true;
    }

    function mock(Ad $ad){
        return null;
    }
}