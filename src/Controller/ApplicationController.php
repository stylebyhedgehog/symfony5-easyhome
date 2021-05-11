<?php


namespace App\Controller;


use App\Data\ApplicationDTO;
use App\Entity\Application;
use App\Form\ApplicationFilterType;
use App\Repository\AdRepository;
use App\Repository\ApplicationRepository;
use App\Repository\ClientRepository;
use App\Service\ApplicationDocumentationService;
use App\Service\constants\AdStatus;
use App\Service\constants\ApplicationStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApplicationController extends AbstractController
{
    private $clientRepository;
    private $adRepository;
    private $entityManager;
    private $applicationRepository;

    public function __construct(ApplicationRepository $applicationRepository,ClientRepository $clientRepository, AdRepository $adRepository,EntityManagerInterface $entityManager)
    {
        $this->clientRepository=$clientRepository;
        $this->adRepository=$adRepository;
        $this->entityManager=$entityManager;
        $this->applicationRepository=$applicationRepository;
    }

    /**
     * @Route ("/client/{id_user}/applications/ad/{id_ad}/create", name="client_application_create")
     * @param int $id_user
     * @param int $id_ad
     * @return RedirectResponse
     */
    public function client_application_create(int $id_user, int $id_ad){
        $application = new Application();
        $client=$this->clientRepository->find($id_user);
        $ad=$this->adRepository->find($id_ad);
        $application->setSender($client);
        $application->setOwner($ad->getOwner());
        $application->setAgent($ad->getAgent());
        $application->setAd($ad);
        $application->setStatus(ApplicationStatus::$status_wait_owner);
        $this->denyAccessUnlessGranted('create',$application);
        $this->entityManager->persist($application);
        $this->entityManager->flush();
        return $this->redirectToRoute(('general_ad_one'),["id_ad"=>$id_ad]);

    }
    /**
     * @Route ("/client/{id_user}/applications/ad/{id_ad}/cancel", name="client_application_remove")
     * @param int $id_user
     * @param int $id_ad
     * @return RedirectResponse
     */
    public function client_application_remove(int $id_user, int $id_ad){
        $application=$this->applicationRepository->findOneBy(['id_sender'=>$id_user,'id_ad'=>$id_ad]);
        $this->denyAccessUnlessGranted('delete',$application);
        $this->entityManager->remove($application);
        $this->entityManager->flush();
        return $this->redirectToRoute(('general_ad_one'),["id_ad"=>$id_ad]);
    }

    /**
     * Собственные отправленные заявки
     * @Route ("/client/{id_user}/applications/sent/", name="client_application_sent_all")
     * @param int $id_user
     * @return Response
     */
    public function client_application_sent_all(int $id_user, Request $request){
        $this->denyAccessUnlessGranted('view_section',$id_user);
        $applicationDTO= new ApplicationDTO();
        $form=$this->createForm(ApplicationFilterType::class,$applicationDTO);
        $form->handleRequest($request);
        $applications=$this->applicationRepository->findByFiltersAndAccessType($applicationDTO,$id_user,"sender");
        return $this->render('application/applicationSentAll.html.twig',[
           'applications'=>$applications,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Входящие заявки
     * @Route ("/client/{id_user}/applications/incoming/", name="client_application_incoming_all")
     * @param int $id_user
     * @param Request $request
     * @return Response
     */
    public function client_application_incoming_all(int $id_user, Request $request){
        $this->denyAccessUnlessGranted('view_section',$id_user);
        $applicationDTO= new ApplicationDTO();
        $form=$this->createForm(ApplicationFilterType::class,$applicationDTO);
        $form->handleRequest($request);
        $applications=$this->applicationRepository->findByFiltersAndAccessType($applicationDTO,$id_user,"owner");
        return $this->render('application/applicationIncomingAll.html.twig',[
            'applications'=>$applications,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Входящие заявки на определенное объявление
     * @Route ("/client/{id_user}/applications/incoming/ad/{id_ad}", name="client_application_incoming_by_ad")
     * @param int $id_user
     * @param int $id_ad
     * @param Request $request
     * @return Response
     */
    public function client_application_incoming_by_ad(int $id_user,int $id_ad, Request $request){
        $this->denyAccessUnlessGranted('view_section',$id_user);
        //todo сервис
        if($this->adRepository->find($id_ad)->getOwner()!==$this->getUser()){
            throw new AccessDeniedException();
        }
        $applicationDTO= new ApplicationDTO();
        $form=$this->createForm(ApplicationFilterType::class,$applicationDTO);
        $form->handleRequest($request);
        $applications=$this->applicationRepository->findByFiltersAndAccessType($applicationDTO,$id_user,"owner",$id_ad);
        return $this->render('application/applicationIncomingAll.html.twig',[
            'applications'=>$applications,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route ("/client/{id_user}/applications/incoming/{id_application}/accept", name="client_application_incoming_accept")
     * @param int $id_user
     * @param int $id_application
     * @return RedirectResponse
     */
    public function client_application_incoming_accept(int $id_user, int $id_application){
        $application=$this->applicationRepository->find($id_application);
        $this->denyAccessUnlessGranted('accept',$application);
        $applications_connected_with_ad=$application->getAd()->getApplications();
        foreach ($applications_connected_with_ad as $application_connected){
            $this->update_status($application_connected, ApplicationStatus::$status_canceled_owner);
        }
        $this->update_status($application,ApplicationStatus::$status_wait_agent);

        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }
    /**
     * @Route ("/client/{id_user}/applications/incoming/{id_application}/cancel", name="client_application_incoming_cancel")
     * @param int $id_user
     * @param int $id_application
     * @return RedirectResponse
     */
    public function client_application_incoming_cancel(int $id_user, int $id_application){
        $application=$this->applicationRepository->find($id_application);
        $this->denyAccessUnlessGranted('cancel',$application);
        $this->update_status($application, ApplicationStatus::$status_canceled_owner);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }

    public function update_status(Application $application, string $status){
        $application->setStatus($status);
        $this->entityManager->persist($application);
        $this->entityManager->flush();
    }

    // AGENT SCOPE

    /**
     * @Route ("/agent/{id_user}/applications/controlled", name="agent_application_controlled")
     * @param int $id_user
     * @return Response
     */
    public function agent_application_controlled(int $id_user){
        $this->denyAccessUnlessGranted('view_section',$id_user);
        $agent=$this->clientRepository->find($id_user);
        $applications = $agent->getApplicationsControlled();
        return $this->render('application/applicationAgentAll.html.twig',[
            'applications'=>$applications
        ]);
    }

    /**
     * @Route ("/agent/{id_user}/applications/controlled/{id_application}/cancel", name="agent_application_controlled_accept")
     * @param int $id_user
     * @param int $id_application
     * @param ApplicationDocumentationService $applicationDocumentationService
     * @return RedirectResponse
     */
    public function agent_application_controlled_accept(int $id_user, int $id_application,ApplicationDocumentationService $applicationDocumentationService){
        $application=$this->applicationRepository->find($id_application);
        $this->denyAccessUnlessGranted('accept',$application);
        $this->update_status($application,ApplicationStatus::$status_accept_agent);
        $ad= $application->getAd();
        $ad->setStatus(AdStatus::$status_wait_deal);
        $this->entityManager->persist($ad);
        $this->entityManager->flush();
        $applicationDocumentationService->generate($application);
        return $this->redirectToRoute('agent_application_controlled',['id_user'=>$id_user]);
    }

    /**
     * @Route ("/client/{id_user}/applications/controlled/{id_application}/cancel", name="agent_application_controlled_cancel")
     * @param int $id_user
     * @param int $id_application
     * @return RedirectResponse
     */
    public function agent_application_controlled_cancel(int $id_user, int $id_application){
        $application=$this->applicationRepository->find($id_application);
        $this->denyAccessUnlessGranted('cancel',$application);
        $this->update_status($application, ApplicationStatus::$status_canceled_agent);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }
}