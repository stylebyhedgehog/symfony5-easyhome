<?php


namespace App\Controller\client;


use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/client/{id_client}")
 */
class ProfileController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;
    public function __construct(ClientRepository $clientRepository,AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository=$clientRepository;
    }

    /**
     * @Route ("/profile",name="client_profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request){
        $client = $this->clientRepository->find($request->get("id_client"));
        return $this->render('client/profile.html.twig', [
            'client' => $client,
        ]);
    }
}