<?php


namespace App\Controller;


use App\Entity\Client;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ClientRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route ("/client/{id_user}/review")
 */
class ReviewController extends AbstractController
{
    private $reviewRepository;
    private $entityManager;
    private $clientRepository;

    /**
     * @param ClientRepository $clientRepository
     * @param ReviewRepository $reviewRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ClientRepository $clientRepository,ReviewRepository $reviewRepository,EntityManagerInterface $entityManager)
    {
        $this->clientRepository=$clientRepository;
        $this->entityManager = $entityManager;
        $this->reviewRepository = $reviewRepository;
    }

    /**
     * @Route ("/", name="client_review_all")
     * @param int $id_user
     * @param Request $request
     * @return Response
     */
    public function all(int $id_user,Request $request){
        $client=$this->clientRepository->find($id_user);
        $reviews=$client->getClientReviews();
        //form
        $form=$this->create($id_user,$request);

        return $this->render('review/review.html.twig', [
            'reviews' =>$reviews,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @param int $id_user
     * @param Request $request
     * @return \Symfony\Component\Form\FormInterface
     */
    public function create(int $id_user,Request $request){
        $review = new Review();
        $form=$this->createForm(ReviewType::class,$review);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $review->setRater($this->getUser());
            $review->setClient($this->clientRepository->find($id_user));
            if ($review->getRater()===$review->getClient()){throw new AccessDeniedException();}
            $this->entityManager->persist($review);
            $this->entityManager->flush();
        }
        return  $form;
    }

}