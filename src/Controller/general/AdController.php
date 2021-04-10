<?php

namespace App\Controller\general;

use App\Entity\Ad;
use App\Entity\AdImage;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Repository\FavoriteRepository;
use App\Service\AdService;
use App\Service\constants\AdEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route ("/ads")
 */
class AdController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;
    private $favoriteRepository;

    public function __construct(FavoriteRepository $favoriteRepository, ClientRepository $clientRepository, AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }


    /**
     * @Route("/", name="general_ad_all")
     * @param Request $request
     * @return Response
     */
    public function all(Request $request)
    {
        $user = $this->getUser();
        $favorites_id = [];
        $ads = $this->adRepository->findAll();

        if ($user) {
            $favorites = $this->clientRepository->find($user)->getFavoriteAds();
            foreach ($favorites as $favorite) {
                array_push($favorites_id, $favorite->getAd()->getId());
            }
        }
        return $this->render('general/adAll.html.twig', [
            'ads' => $ads,
            'favorites_id' => $favorites_id
        ]);
    }

    /**
     * @Route("/{id_ad}", name="general_ad_one")
     * @param Request $request
     * @return Response
     */
    public function one(Request $request)
    {
        $ad = $this->adRepository->find($request->get("id_ad"));
        $user = $this->getUser();
        $favorites_id = [];
        if ($user) {
            $favorites = $this->clientRepository->find($user)->getFavoriteAds();
            foreach ($favorites as $favorite) {
                array_push($favorites_id, $favorite->getAd()->getId());
            }
        }

        return $this->render('general/adOne.html.twig', [
            'ad' => $ad,
            'favorites_id' => $favorites_id
        ]);
    }

}
