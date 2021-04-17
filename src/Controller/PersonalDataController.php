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
     * @Route ("/client/{id_user}/personal_data/",name="client_personal_data")
     * @Route ("/agent/{id_user}/personal_data/",name="agent_personal_data")
     * @param int $id_user
     * @return RedirectResponse|Response
     */
    public function display(int $id_user)
    {
        $personal_data = $this->personalDataRepository->findBy(["client" => $id_user]);
        return $this->render('personalData/personalData.html.twig', [
            'personal_data' => $personal_data,
        ]);
    }

    /**
     * @Route ("/client/{id_user}/personal_data/create",name="client_personal_data_create")
     * @Route ("/agent/{id_user}/personal_data/create",name="agent_personal_data_create")
     * @param Request $request
     * @param int $id_user
     * @return RedirectResponse|Response
     */
    public function create(Request $request, int $id_user)
    {
        $personal_data = new PersonalData();
        $form = $this->createForm(PersonalDataType::class, $personal_data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $personal_data->setClient($this->clientRepository->find($id_user));
            $this->entityManager->persist($personal_data);
            $this->entityManager->flush();
            if($request->attributes->get('_route')== "client_personal_data_create"){
                return $this->redirectToRoute('client_personal_data', ['id_user' => $id_user]);
            }
            else{
                return $this->redirectToRoute('agent_personal_data', ['id_user' => $id_user]);
            }
        }
        return $this->render('personalData/personalDataC.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}