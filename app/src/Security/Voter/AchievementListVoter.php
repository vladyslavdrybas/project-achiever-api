<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\AchievementList;
use App\Entity\User;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use function get_class;
use function var_dump;

final class AchievementListVoter extends AbstractVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on `Achievement` objects
        if (!$subject instanceof AchievementList) {
            return false;
        }

        // if the attribute isn't one we support, return false
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
            // the user must be logged in; if not, deny access
            return false;
        }

        return match($attribute) {
            Permissions::VIEW => $this->canView($subject, $user),
            Permissions::EDIT => $this->canEdit($subject, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    protected function canView(AchievementList $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        if ($subject->isPublic()) {
            return true;
        }

        if ($this->canEdit($subject, $user)) {
            return true;
        }

        // TODO add check for user group members
        foreach ($subject->getListGroupRelations() as $relation) {
            var_dump(get_class($relation));
            exit;
        }

        return false;
    }

    protected function canEdit(AchievementList $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        return $subject->getOwner()->getRawId() === $user->getRawId();
    }
}
