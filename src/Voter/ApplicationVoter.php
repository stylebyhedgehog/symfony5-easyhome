<?php


namespace App\Voter;


use App\Entity\Application;
use App\Service\constants\AdStatus;
use App\Service\constants\ApplicationStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ApplicationVoter extends Voter
{
    const DELETE = 'delete';
    const ACCEPT = 'accept';
    const CANCEL = 'cancel';
    const CREATE = 'create';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {

        if (!in_array($attribute, array(self::ACCEPT, self::CANCEL, self::DELETE,self::CREATE))) {

            return false;
        }

        if (!$subject instanceof Application) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {

        $user = $token->getUser();

        /** @var Application $application */
        $application = $subject;
        if ($user instanceof UserInterface) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }
            switch ($attribute) {
                case self::CANCEL:
                case self::ACCEPT:
                    return $this->canAcceptOrCancel($application, $user);
                case self::DELETE:
                    return $this->canDelete($application, $user);
                case self::CREATE:
                    return $this->canCreate($application, $user);
            }
        } else {
            switch ($attribute) {
                case self::ACCEPT:
                case self::CREATE:
                case self::CANCEL:
                case self::DELETE:
                    return false;
            }
        }
        throw new LogicException();
    }

    // todo accept user accept client
    private function canAcceptOrCancel(Application $application, UserInterface $user)
    {
        if($application->getOwner()===$user and $this->security->isGranted('ROLE_VERIFIED')){
            return $application->getStatusNumber()==ApplicationStatus::$status_wait_owner;
        }
        if($application->getAgent()===$user and $this->security->isGranted('ROLE_VERIFIED')){
            return $application->getStatusNumber()==ApplicationStatus::$status_wait_agent;
        }
        return false;
    }


    private function canDelete(Application $application, UserInterface $user)
    {
        return $this->security->isGranted("ROLE_VERIFIED")
            and $application->getSender() === $user
            and $application->getStatusNumber() != ApplicationStatus::$status_wait_agent
            and $application->getStatusNumber() != ApplicationStatus::$status_accept_agent
            and $application->getStatusNumber() != ApplicationStatus::$status_canceled_agent
            and $application->getStatusNumber() != ApplicationStatus::$status_done;
    }

    private function canCreate(Application $application, UserInterface $user)
    {
        return $this->security->isGranted("ROLE_VERIFIED")
            and !$this->security->isGranted("ROLE_AGENT")
            and $application->getSender()===$user
            and $application->getOwner()!==$user
            and $application->getAd()->getStatus()== AdStatus::$status_ok;

    }
}