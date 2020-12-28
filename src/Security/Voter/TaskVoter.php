<?php

namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\RoleVoter;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject)
    {
        $supportsAttribute = in_array($attribute, ['TASK_CREATE', 'TASK_READ', 'TASK_EDIT', 'TASK_DELETE']);
        $supportsSubject = $subject instanceof Task;

        return $supportsAttribute && $supportsSubject;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'TASK_CREATE':
                 return true;
                break;
            case 'TASK_READ':
                if ($subject->getCreator() === $user) {
                    return true;
                }
                return false;
            case 'TASK_EDIT':
                if ($subject->getCreator() === $user) {
                    return true;
                }
                return false;
            case 'TASK_DELETE':
                if ($subject->getCreator() === $user) {
                    return true;
                }
                return false;
                break;
        }

        return false;
    }
}
