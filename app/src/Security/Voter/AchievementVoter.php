<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Achievement;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AchievementVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on `Achievement` objects
        if (!$subject instanceof Achievement) {
            return false;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [Permissions::VIEW, Permissions::EDIT])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }
        // you know $subject is a Achievement object, thanks to `supports()`
        /** @var Achievement $achievement */
        $achievement = $subject;

        return match($attribute) {
            Permissions::VIEW => $this->canView($achievement, $user),
            Permissions::EDIT => $this->canEdit($achievement, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    protected function canView(Achievement $achievement, User $user): bool
    {
        if ($achievement->isPublic()) {
            return true;
        }

        if ($this->canEdit($achievement, $user)) {
            return true;
        }

        return $achievement->getOwner()->getRawId() === $user->getRawId();
    }

    protected function canEdit(Achievement $achievement, User $user): bool
    {
        return $achievement->getOwner()->getRawId() === $user->getRawId();
    }
}
