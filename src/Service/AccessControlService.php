<?php


namespace App\Service;


use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AccessControlService
{
    private $security;
    private $urlGenerator;
    /**
     * AccessControlService constructor.
     * @param $security
     */
    public function __construct(Security $security,UrlGeneratorInterface $urlGenerator)
    {
        $this->security = $security;
        $this->urlGenerator = $urlGenerator;
    }

//    public function onlyVerifiedUserAnd(UserInterface $user, Entity $entity){
//        if ($this->security->isGranted('ROLE_ADMIN'))
//        if ($this->security->getUser()===$user){
//            return $this->redirectToRoute()
//        }
//    }
}