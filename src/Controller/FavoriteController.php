<?php


namespace App\Controller;


use App\Entity\Favorite;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/client/{id_user}/favorite")
 */
class FavoriteController extends AbstractController
{
    private $favoriteRepository;
    private $entityManager;
    private $clientRepository;
    private $adRepository;

    /**
     * FavoriteController constructor.
     * @param AdRepository $adRepository
     * @param ClientRepository $clientRepository
     * @param FavoriteRepository $favoriteRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(AdRepository $adRepository, ClientRepository $clientRepository,FavoriteRepository $favoriteRepository,EntityManagerInterface $entityManager)
    {
        $this->adRepository = $adRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository=$clientRepository;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * @Route("/", name="client_favorite_all")
     * @param Request $request
     * @return Response
     */
    public function all(Request $request){
        $user=$this->getUser();
        $favorites=$this->clientRepository->find($user)->getFavoriteAds();

        return $this->render('favorite/favoriteAll.html.twig', [
            'favorites' =>$favorites
        ]);

    }

    /**
     * @Route("/create", name="client_favorite_create")
     * @param Request $request
     * @return Response
     */
    public function create(Request $request){
        //TODO РАЗРЕШИТЬ ТОЛЬКО ДЛЯ ХОЗЯИНА
        $favorite=new Favorite();
        $favorite->setClient($this->clientRepository->find($request->get("id_user")));
        $favorite->setAd($this->adRepository->find($request->get("id_ad")));
        $this->entityManager->persist($favorite);
        $this->entityManager->flush();
        //TODO В ШАБЛОН СО ВСЕМИ ОБЪЯВЛЕНИЯМИ НЕ ДОЛЖЕН УХОДИТЬ id_ad +СДЕЛАТЬ ФОРМОЙ
        return $this->redirectToRoute($request->get('current_template'),["id_ad"=>$request->get("id_ad")]);
    }

    /**
     * @Route("/remove", name="client_favorite_remove")
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request){
        $favorite=$this->favoriteRepository->findOneBy(["ad"=>$request->get("id_ad"),"client"=>$request->get("id_user")]);
        $this->entityManager->remove($favorite);
        $this->entityManager->flush();
        //TODO В ШАБЛОН СО ВСЕМИ ОБЪЯВЛЕНИЯМИ НЕ ДОЛЖЕН УХОДИТЬ id_ad
        return $this->redirectToRoute($request->get('current_template'),["id_ad"=>$request->get("id_ad"),"id_user"=>$request->get("id_user")]);
    }
}