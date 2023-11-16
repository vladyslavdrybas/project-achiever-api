<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Security\Permissions;
use App\Security\UserGroupSecurityManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function in_array;

class UserGroupVoter extends AbstractVoter
{
    public function __construct(
        protected readonly UserGroupSecurityManager $userGroupSecurityManager
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof UserGroup) {
            return false;
        }

        if (!in_array($attribute, [Permissions::VIEW, Permissions::EDIT, Permissions::DELETE, Permissions::MANAGE])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            Permissions::VIEW => $this->userGroupSecurityManager->canView($subject, $user),
            Permissions::EDIT => $this->userGroupSecurityManager->canEdit($subject, $user),
            Permissions::DELETE => $this->userGroupSecurityManager->canDelete($subject, $user),
            Permissions::MANAGE => $this->userGroupSecurityManager->canManage($subject, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
