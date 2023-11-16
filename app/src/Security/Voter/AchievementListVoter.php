<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\AchievementList;
use App\Entity\User;
use App\Security\AchievementListSecurityManager;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AchievementListVoter extends AbstractVoter
{
    public function __construct(
        protected readonly AchievementListSecurityManager $achievementListSecurityManager
    ) {}

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$subject instanceof AchievementList) {
            return false;
        }

        if (!in_array(
            $attribute,
            [
                Permissions::VIEW,
                Permissions::EDIT,
            ]
        )) {
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
            Permissions::VIEW => $this->achievementListSecurityManager->canView($subject, $user),
            Permissions::EDIT => $this->achievementListSecurityManager->canEdit($subject, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }
}
