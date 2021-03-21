<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function index(Request $request)
    {
        $client = new Client();

        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $client->setPassword($this->passwordEncoder->encodePassword($client, $client->getPassword()));

            // Set their role
            $client->setRoles(['ROLE_USER']);

            // Save
            $em = $this->getDoctrine()->getManager();
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/greeting.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}