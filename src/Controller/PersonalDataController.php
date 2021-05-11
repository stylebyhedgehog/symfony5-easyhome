<?php


namespace App\Controller;


use App\Entity\PersonalData;
use App\Form\PersonalDataType;
use App\Repository\ClientRepository;
use App\Repository\PersonalDataRepository;
use App\Service\constants\PersonalDataStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PersonalDataController extends AbstractController
{
    private $entityManager;
    private $personalDataRepository;

    /**
     * @param PersonalDataRepository $personalDataRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(PersonalDataRepository $personalDataRepository, EntityManagerInterface $entityManager)
    {
        $this->personalDataRepository = $personalDataRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route ("/client/{id_user}/personal_data/",name="client_personal_data")
     * @param int $id_user
     * @return RedirectResponse|Response
     */
    public function display(int $id_user)
    {
        $personal_data = $this->personalDataRepository->findOneBy(['client' => $id_user]);
        return $this->render('personalData/personalData.html.twig', [
            'personal_data' => $personal_data,
        ]);
    }

    /**
     * @Route ("/client/{id_user}/personal_data/create",name="client_personal_data_create")
     * @param Request $request
     * @param int $id_user
     * @return RedirectResponse|Response
     */
    public function create(Request $request, int $id_user)
    {
        $this->denyAccessUnlessGranted('view_section', $id_user);
        $personal_data = new PersonalData();
        $form = $this->createForm(PersonalDataType::class, $personal_data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = $this->getUser()->addRoles('ROLE_VERIFIED');
            $personal_data->setClient($client);
            $personal_data->setStatus(PersonalDataStatus::$status_ok);
            $this->entityManager->persist($client);
            $this->entityManager->persist($personal_data);
            $this->entityManager->flush();
            return $this->redirectToRoute('client_personal_data', ['id_user' => $id_user]);
        }
        return $this->render('personalData/personalDataC.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}