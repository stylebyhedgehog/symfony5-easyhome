<?php


namespace App\Voter;


use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\LogicException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class SectionVoter extends Voter
{
    const VIEW='view_section';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    protected function supports(string $attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW))) {
            return false;
        }
        if (gettype($subject)!="integer") {

            return false;
        }
        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($user instanceof UserInterface) {
            if ($this->security->isGranted('ROLE_ADMIN')) {
                return true;
            }
            return $subject==$user->getId();
        }
        return false;
    }
}