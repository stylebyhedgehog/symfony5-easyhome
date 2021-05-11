<?php


namespace App\Voter;


use App\Entity\Ad;
use App\Entity\Client;
use App\Service\constants\AdStatus;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AdVoter extends Voter
{
    const DELETE = 'delete';
    const VIEW = 'view';
    const EDIT = 'edit';
    const CREATE = 'create';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject)
    {

        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        if (!$subject instanceof Ad) {
            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        /** @var Ad $ad */
        $ad = $subject;
        if ($user instanceof UserInterface) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }
            switch ($attribute) {
                case self::VIEW:
                    return $this->canView($ad, $user);
                case self::EDIT:
                    return $this->canEdit($ad, $user);
                case self::DELETE:
                    return $this->canDelete($ad, $user);
                case self::CREATE:
                    return $this->canCreate($ad, $user);
            }
        } else {
            switch ($attribute) {
                case self::VIEW and ($ad->getStatus() == AdStatus::$status_ok):
                    return true;
                case self::DELETE:
                case self::EDIT:
                case self::CREATE:
                    return false;
            }
        }
        throw new LogicException();
    }

    private function canView(Ad $ad, UserInterface $user)
    {
        return $ad->getStatus() == AdStatus::$status_ok
            or $user === $ad->getOwner()
            or $this->security->isGranted('ROLE_AGENT');
    }

    private function canEdit(Ad $ad, UserInterface $user)
    {
        return $ad->getOwner() === $user
            and $ad->getStatus() !== AdStatus::$status_wait_deal
            and $ad->getStatus() !== AdStatus::$status_rented
            or $this->security->isGranted('ROLE_AGENT')
            and $user === $ad->getAgent();
    }

    private function canDelete(Ad $ad, UserInterface $user)
    {
        return $ad->getOwner() === $user
            and $ad->getStatus() !== AdStatus::$status_wait_deal
            and $ad->getStatus() !== AdStatus::$status_rented;
    }

    private function canCreate(Ad $ad, UserInterface $user)
    {
        return $this->security->isGranted("ROLE_VERIFIED")
            and !$this->security->isGranted("ROLE_AGENT")
            and $ad->getOwner() === $user;

    }
}