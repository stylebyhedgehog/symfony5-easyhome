<?php


namespace App\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class Greeting extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * @Route ("/greeting", name="greeting_page")
     * @return Response
     */
    public function greet(){
        if ($this->security->isGranted('ROLE_USER')) {
            $message="welcome";
        }
        else{
            $message="who are u";
        }
        return $this->render('greeting.html.twig', [
            'message' => $message,
        ]);
    }
}