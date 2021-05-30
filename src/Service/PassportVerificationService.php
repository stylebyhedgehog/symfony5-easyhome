<?php


namespace App\Service;


use App\Entity\PersonalData;
use App\Repository\PersonalDataRepository;

class PassportVerificationService
{

    private $personalDataRepository;

    /**
     * AdVerificationService constructor.
     * @param PersonalDataRepository $personalDataRepository
     */
    public function __construct(PersonalDataRepository $personalDataRepository)
    {
        $this->personalDataRepository = $personalDataRepository;
    }

    function getDataByPassport(string $passport){
        $token = "18146cd0fd1b5aa3fc838c2ab74c3e7e67ef4520";
        $secret = "6b724d29d61dceafaa93bf7f53b80fec8027616a";
        $dadata = new \Dadata\DadataClient($token, $secret);
        return $dadata->clean("passport", $passport);
    }
    function checkPassport(PersonalData $personalData){
        if (!$this->uniqCheck($personalData)){
            return "Пользователь с данным паспортом уже существует";
        }
        $passport=$personalData->getPassport();

        $result = $this->getDataByPassport($passport);
        switch ($this->getCode($result)){
            case 2:
                return "Исходное значение пустое";
            case 1:
                return "Неправильный формат серии или номера";
            case 10:
                return "Недействительный паспорт";
        }
        return null;
    }
    function uniqCheck(PersonalData $pData){
        $pDatas=$this->personalDataRepository->findAll();
        foreach ($pDatas as $pDat){
            if ($pData->getPassport()===$pDat->getPassport()){
                return false;
            }
        }
        return true;
    }

    function getCode(array $result){
        return $result["qc"];
    }

    function mock(PersonalData $pData){
        return null;
    }
}