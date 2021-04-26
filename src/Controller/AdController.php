<?php

namespace App\Controller;


use App\Data\AdData;
use App\Entity\Ad;
use App\Entity\AdImage;
use App\Form\AdFilterType;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\BrowsingHistoryRepository;
use App\Repository\ClientRepository;
use App\Repository\FavoriteRepository;
use App\Service\AdAgentService;

use App\Service\AdRecommendationService;
use App\Service\constants\AdStatus;
use App\Service\constants\Mode;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;
    private $favoriteRepository;
    private $browsingHistoryRepository;

    public function __construct(BrowsingHistoryRepository $browsingHistoryRepository,FavoriteRepository $favoriteRepository, ClientRepository $clientRepository, AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->favoriteRepository = $favoriteRepository;
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
        $this->browsingHistoryRepository=$browsingHistoryRepository;
    }


    /**
     * @Route("/ads/", name="general_ad_all")
     * @param Request $request
     * @return Response
     */
    public function general_ad_all(Request $request)
    {
        $data= new AdData();
        $form= $this->createForm(AdFilterType::class,$data);
        $form->handleRequest($request);
        $ads =$this->adRepository->findByClientFilters($data, Mode::$agent_ad_controlled);

        return $this->render('ad/adAll.html.twig', [
            'ads' => $ads,
            'form'=> $form->createView()
        ]);
    }
    /**
     * @Route("/ads/recommended", name="general_ad_all_recommended")
     * @param Request $request
     * @return Response
     */
    public function general_ad_all_recommended(Request $request,AdRecommendationService $adRecommendationService)
    {
        //TODO ДОБАВИТЬ УВЕДОМЛЕНИЕ О НЕУДАЧНОМ ПОДБОРЕ
        $data= new AdData();
        $form= $this->createForm(AdFilterType::class,$data);
        $form->handleRequest($request);
        $recommendation= $adRecommendationService->getRecommendation($this->getUser());
        $ads =$this->adRepository->findByClientFilters($data, Mode::$recommendation_ads,$recommendation);

        return $this->render('ad/adAll.html.twig', [
            'ads' => $ads,
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/ads/{id_ad}", name="general_ad_one")
     * @param Request $request
     * @param AdRecommendationService $adRecommendationService
     * @return Response
     */
    public function general_ad_one(Request $request,AdRecommendationService $adRecommendationService)
    {
        $ad = $this->adRepository->find($request->get("id_ad"));
        if ($this->isGranted("ROLE_USER") and !$this->isGranted("ROLE_AGENT")){
            $user = $this->getUser();
            $adRecommendationService->push($ad,$user,$this->entityManager);
        }
        return $this->render('ad/adOne.html.twig', [
            'ad' => $ad,
            'current_template' => 'general_ad_one'
        ]);
    }

    /**
     * @Route ("/agent/{id_user}/ads/controlled/",name="agent_ad_all")
     * @param Request $request
     * @param int $id_user
     * @return Response
     */
    public function agent_ad_all(Request $request, int $id_user)
    {
        $data= new AdData();
        $form= $this->createForm(AdFilterType::class,$data,['mode'=>"date_choices"]);
        $form->handleRequest($request);
        $ads =$this->adRepository->findByUserFiltersWithMode($data,Mode::$agent_ad_controlled, $id_user);

        return $this->render('ad/adAgentAll.html.twig', [
            'ads' => $ads,
            'form'=>$form->createView()
        ]);
    }

    //TODO В ОДИН ЭКШОН
    /**
     * @Route("/agent/{id_user}/ads/controlled/{id_ad}", name="agent_ad_one")
     * @param Request $request
     * @return Response
     */
    public function agent_ad_one(Request $request)
    {
        $ad = $this->adRepository->find($request->get("id_ad"));

        if ($this->isGranted('ROLE_AGENT')) {
            $form = $this->update_status($ad, $request);
        }

        return $this->render('ad/adOne.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    public function update_status(Ad $ad, Request $request)
    {

        $form = $this->createFormBuilder($ad)
            ->add('status', ChoiceType::class,
                ['choices' => AdStatus::$status,
                    'attr'=>['class'=>'btn btn-secondary dropdown-toggle shadow-none'],
                    'data' => $ad->getStatusNumber()])
            ->add('save', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($ad);
            $this->entityManager->flush();
        }
        return $form;
    }


    /**
     * @Route ("/client/{id_user}/ads/own/",name="client_ad_own_all")
     * @param Request $request
     * @param int $id_user
     * @return Response
     */
    public function client_ad_own_all(Request $request, int $id_user)
    {
        $adData=new AdData();
        $form= $this->createForm(AdFilterType::class,$adData);
        $form->handleRequest($request);
        $ads= $this->adRepository->findByUserFiltersWithMode($adData,Mode::$client_ad_posted,$id_user);

        return $this->render('ad/adOwnAll.html.twig', [
            'ads' => $ads,
            'form' =>$form->createView()
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/create", name="client_ad_own_create")
     * @param Request $request
     * @param AdAgentService $adService
     * @return Response
     */
    //TODO отдельный сервис для загрузки файлов
    public function client_ad_own_create(Request $request, AdAgentService $adService)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $owner = $this->clientRepository->find($request->get("id_user"));
            $ad->setOwner($owner);
            $current_time = new DateTime();
            //TODO РЕАЛИЗОВАТЬ
            $agent = $adService->getAgent($this->clientRepository, $this->adRepository);
            $ad->setAgent($agent);
            $ad->setCreateDate($current_time);
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdStatus::$status_wait);

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
            return $this->redirectToRoute('client_ad_own_all', ['id_user' => $request->get("id_user")]);
        }
        return $this->render('ad/adOwnC.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/{id_ad}/update", name="client_ad_own_update")
     * @param Request $request
     * @param int $id_user
     * @param int $id_ad
     * @return Response
     */
    public function client_ad_own_update(Request $request,int $id_user, int $id_ad){
        //TODO ПОДГРУЗКА КАРТИНКИ
        $ad = $this->adRepository->find($id_ad);
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        return $this->render('ad/adOwnC.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/{id_ad}/delete", name="client_ad_own_delete")
     * @param int $id_user
     * @param int $id_ad
     * @return Response
     */
    //TODO УДАЛЕНИЕ ФАЙЛА КАРТИНКИ ПРИ УДАЛЕНИИ ОБЪЯВЛЕНИЯ
    public function client_ad_own_delete(int $id_user, int $id_ad){
        $ad=$this->adRepository->find($id_ad);
        $this->entityManager->remove($ad);
        $this->entityManager->flush();
        return $this->redirectToRoute('general_ad_all');
    }
    public function client_ad_own_edit(){

    }
}
