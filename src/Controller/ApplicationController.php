<?php


namespace App\Controller;


use App\Data\ApplicationData;
use App\Entity\Application;
use App\Form\ApplicationFilterType;
use App\Repository\AdRepository;
use App\Repository\ApplicationRepository;
use App\Repository\ClientRepository;
use App\Service\constants\ApplicationStatus;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $user=$this->clientRepository->find($id_user);
        $ad=$this->adRepository->find($id_ad);
        $application->setSender($user);
        $application->setOwner($ad->getOwner());
        $application->setAgent($ad->getAgent());
        $application->setAd($ad);
        $application->setCreateDate(new DateTime());
        $application->setStatus(ApplicationStatus::$status_wait_owner);
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
        $applicationData= new ApplicationData();
        $form=$this->createForm(ApplicationFilterType::class,$applicationData);
        $form->handleRequest($request);
        $applications=$this->applicationRepository->findByFiltersAndAccessType($applicationData,$id_user,"sender");
        return $this->render('application/applicationSentAll.html.twig',[
           'applications'=>$applications,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Входящие заявки
     * @Route ("/client/{id_user}/applications/incoming/", name="client_application_incoming_all")
     * @param int $id_user
     * @return Response
     */
    public function client_application_incoming_all(int $id_user, Request $request){
        $applicationData= new ApplicationData();
        $form=$this->createForm(ApplicationFilterType::class,$applicationData);
        $form->handleRequest($request);
        $applications=$this->applicationRepository->findByFiltersAndAccessType($applicationData,$id_user,"owner");
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
//        TODO ВСЕ ЗАЯВКИ ПОМИМО ПРИНЯТОЙ ДОЛЖНЫ БЫТЬ ОТКЛОНЕНЫ ДЛЯ ДАННОГО ОБЪЯВЛЕНИЯ
        $this->update_status($id_application,ApplicationStatus::$status_wait_agent);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }
    /**
     * @Route ("/client/{id_user}/applications/incoming/{id_application}/cancel", name="client_application_incoming_cancel")
     * @param int $id_user
     * @param int $id_application
     * @return RedirectResponse
     */
    public function client_application_incoming_cancel(int $id_user, int $id_application){
        $this->update_status($id_application, ApplicationStatus::$status_canceled_owner);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }

    public function update_status(int $id_application, string $status){
        $application = $this->applicationRepository->find($id_application);
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
        //TODO ОТОБРАЖАТЬ ТОЛЬКО СО СТАТУСОМ ПОДТВЕРЖДЕНО ХОЗЯИНОМ
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
     * @return RedirectResponse
     */
    public function agent_application_controlled_accept(int $id_user, int $id_application){
        $this->update_status($id_application, ApplicationStatus::$status_accept_agent);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }

    /**
     * @Route ("/client/{id_user}/applications/controlled/{id_application}/cancel", name="agent_application_controlled_cancel")
     * @param int $id_user
     * @param int $id_application
     * @return RedirectResponse
     */
    public function agent_application_controlled_cancel(int $id_user, int $id_application){
        $this->update_status($id_application, ApplicationStatus::$status_canceled_agent);
        return $this->redirectToRoute('client_application_incoming_all',['id_user'=>$id_user]);
    }
}