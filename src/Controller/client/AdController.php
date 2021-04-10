<?php

namespace App\Controller\client;

use App\Entity\Ad;
use App\Entity\AdImage;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Service\AdService;
use App\Service\constants\AdEnum;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route ("/client/{id_client}/ads/own_ads")
 */
class AdController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;
    private $security;
    public function __construct(Security  $security,ClientRepository $clientRepository,AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->security=$security;
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository=$clientRepository;
    }

    /**
     * @Route ("/",name="client_ad_own_all")
     * @param Request $request
     * @return Response
     * @param int $id_client
     */
    public function all(Request $request, int $id_client)
    {
        $client = $this->clientRepository->find($id_client);
        if($this->getUser()!= $client)
        {throw $this->createAccessDeniedException('Ошибка доступа');}

            $ads = $client->getPostedAds();
        return $this->render('client/ad/adOwnAll.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/create", name="client_ad_own_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $owner = $this->clientRepository->find($request->get("id_client"));
            $ad->setOwner($owner);
            $current_time = new DateTime();
            //TODO РЕАЛИЗОВАТЬ
            $agent = $this->clientRepository->find(AdService::getAgent());
            $ad->setAgent($agent);
            $ad->setCreateDate($current_time);
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdEnum::$status_wait_verification);

            $images = $form->get('images')->getData();
            $upload_directory = "images";

            foreach ($images as $image) {
                $imageAd = new AdImage();
                //TODO ШИФРОВАТЬ
                $originalFilename = $image->getClientOriginalName();
                $image->move(
                    $upload_directory,
                    $originalFilename
                );
                $imageAd->setFilename($originalFilename);
                $ad->addImage($imageAd);
            }
            $this->entityManager->persist($ad);
            $this->entityManager->flush();
            return $this->redirectToRoute('client_ad_own_all',['id_client'=>$request->get("id_client")]);
        }
        return $this->render('client/ad/adOwnC.html.twig', [
            'form' => $form->createView(),
        ])
            ;

    }

}
