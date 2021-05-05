<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RegionCityController extends AbstractController
{

    private $data;
    public function __construct()
    {
        $strJsonFileContents = file_get_contents(getcwd() . "\\json\\russia.json");
        $this->data = json_decode($strJsonFileContents, TRUE);
    }

    public function getAllRegions()
    {
        $all = [];
        foreach ($this->data as $item) {
            array_push($all, $item["region"]);
        }
        return array_unique($all);
    }

    public function getAllCities()
    {
        $all = [];
        foreach ($this->data as $item) {
            $all[$item["city"]]= $item["city"];
        }
        sort($all);
        return $all;
    }

    /**
     * @Route("/service/cities_in_region/{region}", name="cities_in_region")
     * @param string $region
     * @return Response
     */
    public function getCitiesInRegion(string $region)
    {
        $all = [];
        foreach ($this->data as $item) {
            if ($item["region"] == $region) {
                $all[$item["city"]]=$item["city"];
            }
        }
        return new JsonResponse($all);
    }
}