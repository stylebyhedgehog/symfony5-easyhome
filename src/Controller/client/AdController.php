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
     * @Route("/create", name="client_ad_own_create", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function ownCreate(Request $request)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class,$ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $owner=$this->clientRepository->find($request->get("id_client"));
            $ad->setOwner($owner);
            $current_time=new DateTime();
            $agent=$this->clientRepository->find(AdService::getAgent());
            $ad->setAgent($agent);
            $ad->setCreateDate($current_time);
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdEnum::$status_wait_verification);

            $images = $form->get('images')->getData();
            //TODO НОРМАЛЬНЫЙ ПУТЬ
            $upload_directory="images";

            foreach ($images as $image){
                $imageAd= new AdImage();
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
//            return $this->redirectToRoute('client_ad_own_all');
        }
        return $this->render('client/adOwnCE.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @Route("/", name="client_ad_own_all")
     * @param Request $request
     * @return Response
     */
    public function ownAll(Request $request)
    {

        $ads = $this->adRepository->findBy(array("owner"=>$request->get("id_client")));
        return $this->render('client/adOwnAll.html.twig', [
            'ads' => $ads,
        ]);
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
