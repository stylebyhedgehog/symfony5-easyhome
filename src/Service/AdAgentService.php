<?php


namespace App\Service;


use App\Repository\AdRepository;
use App\Repository\ClientRepository;

class AdAgentService
{
    //TODO обработка ошибок
    //Поиск агента с наименьшей занятостью (статус объявления

    public function getAgent(ClientRepository $clientRepository, AdRepository $adRepository){
        $clients=$clientRepository->findAll();
        $agents=[];

        foreach ($clients as $client){
            if(in_array('ROLE_AGENT',$client->getRoles())){
                array_push($agents, $client);
            }
        }
        $min_count =99999;
        $min_agent =$agents[0];
        foreach ($agents as $agent){
            if(count($agent->getControlledAds())< $min_count)
            {
                $min_count =count($agent->getControlledAds())< $min_count;
                $min_agent =$agent;
            }
        }
        return $min_agent;
    }

}