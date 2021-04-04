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

/**
 * @Route ("/client/{id_client}")
 */
class ProfileController extends AbstractController
{
    private $adRepository;
    private $entityManager;
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository, AdRepository $adRepository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route ("/profile",name="client_profile")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        $client = $this->clientRepository->find($request->get("id_client"));
        return $this->render('client/profile/profile.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route ("/profile/personal_data",name="client_profile_data")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function personal_data(Request $request)
    {

        return $this->render('client/profile/profileBlockPersonalData.html.twig');
    }

    /**
     * @Route ("/profile/own_ads",name="client_profile_ad_own_all")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ad_own_all(Request $request)
    {
        $client = $this->clientRepository->find($request->get("id_client"));
        $ads = $client->getPostedAds();
        return $this->render('client/profile/profileBlockAdOwnAll.html.twig', [
            'ads' => $ads,
        ]);
    }

    /**
     * @Route("/profile/own_ads/create", name="client_profile_ad_own_create")
     * @param Request $request
     * @return Response
     */
    public function ad_own_create(Request $request)
    {
        $ad = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $owner = $this->clientRepository->find($request->get("id_client"));
            $ad->setOwner($owner);
            $current_time = new DateTime();
            $agent = $this->clientRepository->find(AdService::getAgent());
            $ad->setAgent($agent);
            $ad->setCreateDate($current_time);
            $ad->setUpdateDate($current_time);
            $ad->setStatus(AdEnum::$status_wait_verification);

            $images = $form->get('images')->getData();
            //TODO НОРМАЛЬНЫЙ ПУТЬ
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
            return $this->redirectToRoute('client_profile_ad_own_all',['id_client'=>$request->get("id_client")]);
        }
        return $this->render('client/profile/profileBlockAdOwnC.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    public function ad_own_edit(){

    }

    /**
     * @Route("/profile/favorite", name="client_profile_favorite")
     * @param Request $request
     * @return Response
     */
    public function favorite(Request $request){
        $user=$this->get('security.token_storage')->getToken()->getUser();
        $favorites=$this->clientRepository->find($user)->getFavoriteAds();
        $favorite_ads=[];
        foreach($favorites as $favorite){
            array_push($favorite_ads, $favorite->getAd());
        }
        return $this->render('client/profile/profileBlockFavorite.html.twig', [
            'favorites' =>$favorite_ads
        ]);
    }

}