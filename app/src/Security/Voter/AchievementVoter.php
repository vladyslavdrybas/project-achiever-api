<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Achievement;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AchievementVoter extends Voter
{
    const READ = 'read';
    const UPDATE = 'update';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on `Achievement` objects
        if (!$subject instanceof Achievement) {
            return false;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::READ, self::UPDATE])) {
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
            self::READ => $this->canRead($achievement, $user),
            self::UPDATE => $this->canUpdate($achievement, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    protected function canRead(Achievement $achievement, User $user): bool
    {
        if ($achievement->isPublic()) {
            return true;
        }

        if ($this->canUpdate($achievement, $user)) {
            return true;
        }

        return $achievement->getOwner()->getRawId() === $user->getRawId();
    }

    protected function canUpdate(Achievement $achievement, User $user): bool
    {
        return $achievement->getOwner()->getRawId() === $user->getRawId();
    }
}
