<?php

namespace App\Controller\client;

use App\Entity\Ad;
use App\Entity\AdImage;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Service\AdService;
use App\Service\enum\AdEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route ("/client/{id_client}/ads")
 */
class AdController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;
    public function __construct(ClientRepository $clientRepository,AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository=$clientRepository;
    }





    /**
     * @Route("/{id_ad}", name="client_ad_one")
     * @param Request $request
     * @return Response
     */
    public function one(Request $request)
    {

        $ad = $this->adRepository->find($request->get("id_ad"));
        return $this->render('client/adOne.html.twig', [
            'ad' => $ad,
        ]);
    }

}
