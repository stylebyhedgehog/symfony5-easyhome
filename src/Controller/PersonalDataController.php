<?php


namespace App\Controller;


use App\Entity\PersonalData;
use App\Form\PersonalDataType;
use App\Repository\AdRepository;
use App\Repository\ClientRepository;
use App\Repository\FavoriteRepository;
use App\Repository\PersonalDataRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/client/{id_client}/personal_data")
 */
class PersonalDataController extends AbstractController
{
    private $entityManager;
    private $clientRepository;
    private $personalDataRepository;

    /**
     * @param PersonalDataRepository $personalDataRepository
     * @param ClientRepository $clientRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PersonalDataRepository $personalDataRepository, ClientRepository $clientRepository, EntityManagerInterface $entityManager)
    {
        $this->personalDataRepository = $personalDataRepository;
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * @Route ("/",name="client_personal_data")
     * @param int $id_client
     * @return RedirectResponse|Response
     */
    public function display(int $id_client)
    {
        $personal_data = $this->personalDataRepository->findBy(["client" => $id_client]);
        return $this->render('personalData/personalData.html.twig', [
            'personal_data' => $personal_data,
        ]);
    }

    /**
     * @Route ("/create",name="client_personal_data_create")
     * @param Request $request
     * @param int $id_client
     * @return RedirectResponse|Response
     */
    public function create(Request $request, int $id_client)
    {
        $personal_data = new PersonalData();
        $form = $this->createForm(PersonalDataType::class, $personal_data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personal_data->setClient($this->clientRepository->find($id_client));
            $this->entityManager->persist($personal_data);
            $this->entityManager->flush();
            return $this->redirectToRoute('client_personal_data', ['id_client' => $id_client]);
        }
        return $this->render('personalData/personalDataC.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}