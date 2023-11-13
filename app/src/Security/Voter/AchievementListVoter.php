<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\AchievementList;
use App\Entity\User;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AchievementListVoter extends AbstractVoter
{
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

        foreach ($subject->getListGroupRelations() as $relation) {
            /** @var \App\Entity\UserGroup $relation */
            foreach ($relation->getUserGroupRelations() as $userGroupRelation) {
                if ($userGroupRelation->getMember() === $user) {
                    return $userGroupRelation->isCanView();
                }
            }
        }

        return false;
    }

    protected function canEdit(AchievementList $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        foreach ($subject->getListGroupRelations() as $relation) {
            /** @var \App\Entity\UserGroup $relation */
            foreach ($relation->getUserGroupRelations() as $userGroupRelation) {
                if ($userGroupRelation->getMember() === $user) {
                    return $userGroupRelation->isCanEdit();
                }
            }
        }

        return false;
    }
}
