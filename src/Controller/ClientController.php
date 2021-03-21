<?php


namespace App\Controller;


use App\Entity\Ad;
use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{

    /**
     * @Route("/greeting", name="list")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index()
    {
        return $this->render('global/greeting.html.twig');
    }

}