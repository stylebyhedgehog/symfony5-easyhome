<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Entity\AdDto;
use App\Form\AdFilterType;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Service\AdAgentService;
use App\Service\AdRecommendationService;
use App\Service\AdVerificationService;
use App\Service\constants\AdStatus;
use App\Service\FileUploaderService;
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

    public function __construct( ClientRepository $clientRepository, AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route("/ads/", name="general_ad_all")
     * @param Request $request
     * @return Response
     */
    public function general_ad_all(Request $request): Response
    {
        $data = new AdDto();
        $form = $this->createForm(AdFilterType::class, $data);
        $form->handleRequest($request);
        $ads = $this->adRepository->findAllAdsByFilters($data);
        return $this->render('ad/adAll.html.twig', [
            'ads' => $ads,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/ads/{id_ad<\d+>}", name="general_ad_one")
     * @param int $id_ad
     * @param Request $request
     * @param AdRecommendationService $adRecommendationService
     * @return Response
     */
    public function general_ad_one(int $id_ad,Request $request, AdRecommendationService $adRecommendationService)
    {

        $ad = $this->adRepository->find($id_ad);
        if(!($this->isGranted("ROLE_AGENT") or $this->isGranted("ROLE_ADMIN"))){
            if ($this->isGranted("ROLE_USER")) {
                $user = $this->getUser();
                $adRecommendationService->push($ad, $user);
            }
            return $this->render('ad/adOne.html.twig', [
                'ad' => $ad
            ]);
        }
        else{
            $form = $this->update_status($ad, $request);
            return $this->render('ad/adOne.html.twig', [
                'ad' => $ad,
                'form' => $form->createView()
            ]);
        }
    }
    public function update_status(Ad $ad, Request $request)
    {
        $form = $this->createFormBuilder($ad)
            ->add('status', ChoiceType::class,
                ['choices' => AdStatus::$status,
                    'attr' => ['class' => 'btn btn-secondary dropdown-toggle shadow-none'],
                    'data' => $ad->getStatusNumber()])
            ->add('save', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->denyAccessUnlessGranted('edit',$ad);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();
        }
        return $form;
    }
    /**
     * @Route("/ads/recommended", name="general_ad_all_recommended")
     * @param Request $request
     * @param AdRecommendationService $adRecommendationService
     * @return Response
     */
    public function general_ad_all_recommended(Request $request, AdRecommendationService $adRecommendationService)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $data = new AdDto();
        $form = $this->createForm(AdFilterType::class, $data);
        $form->handleRequest($request);
        $recommendation = $adRecommendationService->getRecommendation($this->getUser());
        $ads = $this->adRepository->findRecommendAdsByFilters($data, $recommendation);
        return $this->render('ad/adAll.html.twig', [
            'ads' => $ads,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/agent/{id_user}/ads/controlled/",name="agent_ad_controlled")
     * @param Request $request
     * @param int $id_user
     * @return Response
     */
    public function agent_ad_controlled(Request $request,int $id_user)
    {
        $this->denyAccessUnlessGranted('view_section',$id_user);
        $data = new AdDto();
        $form = $this->createForm(AdFilterType::class, $data);
        $form->handleRequest($request);
        $ads = $this->adRepository->findControlledAdsByFilters($data, $id_user);

        return $this->render('ad/adAgentAll.html.twig', [
            'ads' => $ads,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/client/{id_user}/ads/own/",name="client_ad_own_all")
     * @param Request $request
     * @param int $id_user
     * @return Response
     */
    public function client_ad_own_all(Request $request,int $id_user)
    {
        $this->denyAccessUnlessGranted('view_section',$id_user);
        $adDTO = new AdDto();
        $form = $this->createForm(AdFilterType::class, $adDTO);
        $form->handleRequest($request);
        $ads = $this->adRepository->findPostedAdsByFilters($adDTO, $id_user);

        return $this->render('ad/adOwnAll.html.twig', [
            'ads' => $ads,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/create", name="client_ad_own_create")
     * @param Request $request
     * @param FileUploaderService $fileUploadService
     * @param AdAgentService $adService
     * @param AdVerificationService $adVerificationService
     * @param AdRepository $adRepository
     * @return Response
     */

    public function client_ad_own_create(Request $request, FileUploaderService $fileUploadService,AdAgentService $adService, AdVerificationService $adVerificationService, AdRepository $adRepository)
    {
        $error = "";
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $error = $adVerificationService->checkAddress($ad);
            if ($error != null) {
                return $this->render('ad/adOwnCE.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }
            $owner = $this->getUser();
            $ad->setOwner($owner);
            $current_time = new DateTime();
            $agent = $adService->getAgent($this->clientRepository, $this->adRepository);
            $ad->setAgent($agent);
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdStatus::$status_wait);
            $this->denyAccessUnlessGranted('create',$ad);
            $images = $form->get('images')->getData();
            $fileUploadService->uploadImage($images,$ad);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();
            return $this->redirectToRoute('client_ad_own_all', ['id_user' => $request->get("id_user")]);
        }
        return $this->render('ad/adOwnCE.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
            'mode' => "create"
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/{id_ad}/update", name="client_ad_own_update")
     * @param AdVerificationService $adVerificationService
     * @param FileUploaderService $fileUploaderService
     * @param Request $request
     * @param int $id_ad
     * @return Response
     */
    public function client_ad_own_update(AdVerificationService $adVerificationService, FileUploaderService $fileUploaderService,Request $request, int $id_ad)
    {
        $ad = $this->adRepository->find($id_ad);
        $this->denyAccessUnlessGranted('edit', $ad);
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()){
            $error = $adVerificationService->mock($ad);
            if ($error != null) {
                return $this->render('ad/adOwnCE.html.twig', [
                    'form' => $form->createView(),
                    'error' => $error
                ]);
            }
            $current_time = new DateTime();
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdStatus::$status_wait);
            $this->denyAccessUnlessGranted('create',$ad);
            $images = $form->get('images')->getData();
            $fileUploaderService->uploadImage($images,$ad);
            $this->entityManager->persist($ad);
            $this->entityManager->flush();
            return $this->redirectToRoute('client_ad_own_all', ['id_user' => $request->get("id_user")]);
        }
        return $this->render('ad/adOwnCE.html.twig', [
            'form' => $form->createView(),
            'error' => null,
            'mode' => "update"
        ]);
    }

    /**
     * @Route("/client/{id_user}/ads/own/{id_ad}/delete", name="client_ad_own_delete")
     * @param int $id_ad
     * @return Response
     */
    public function client_ad_own_delete(int $id_ad,FileUploaderService $fileUploaderService)
    {
        $ad = $this->adRepository->find($id_ad);
        $this->denyAccessUnlessGranted('delete', $ad);
        $this->entityManager->remove($ad);
        $this->entityManager->flush();
        $fileUploaderService->removeImagesByAd($ad);
        return $this->redirectToRoute('general_ad_all');
    }

}
