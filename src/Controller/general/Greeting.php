<?php


namespace App\Controller\general;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class Greeting extends AbstractController
{
    /**
     * @Route ("/greeting", name="greeting_page")
     */
    public function greet(){
        return $this->render('global/greeting.html.twig', [
            'message' => "welcome",
        ]);
    }
}