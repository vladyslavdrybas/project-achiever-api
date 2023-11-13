<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Achievement;
use App\Security\Permissions;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class AchievementVoter extends AbstractVoter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        // only vote on `Achievement` objects
        if (!$subject instanceof Achievement) {
            return false;
        }

        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [
            Permissions::VIEW,
            Permissions::EDIT,
            Permissions::DELETE,
        ])) {
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
            Permissions::DELETE => $this->canDelete($achievement, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    protected function canView(Achievement $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        foreach ($subject->getLists() as $list) {
            /** @var \App\Entity\AchievementList $list */
            if ($this->isOwner($list, $user)) {
                return true;
            }

            foreach ($list->getListGroupRelations() as $group) {
                /** @var \App\Entity\UserGroup $group */
                if ($this->isOwner($group, $user)) {
                    return true;
                }

                foreach ($group->getUserGroupRelations() as $relation) {
                    /** @var \App\Entity\UserGroupRelation $relation */
                    if ($relation->getMember() === $user) {
                        return $relation->isCanView();
                    }
                }
            }
        }

        return false;
    }

    protected function canEdit(Achievement $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        foreach ($subject->getLists() as $list) {
            /** @var \App\Entity\AchievementList $list */
            if ($this->isOwner($list, $user)) {
                return true;
            }

            foreach ($list->getListGroupRelations() as $group) {
                /** @var \App\Entity\UserGroup $group */
                if ($this->isOwner($group, $user)) {
                    return true;
                }

                foreach ($group->getUserGroupRelations() as $relation) {
                    /** @var \App\Entity\UserGroupRelation $relation */
                    if ($relation->getMember() === $user) {
                        return $relation->isCanEdit();
                    }
                }
            }
        }

        return false;
    }

    protected function canDelete(Achievement $subject, User $user): bool
    {
        if ($this->isOwner($subject, $user)) {
            return true;
        }

        foreach ($subject->getLists() as $list) {
            /** @var \App\Entity\AchievementList $list */
            if ($this->isOwner($list, $user)) {
                return true;
            }

            foreach ($list->getListGroupRelations() as $group) {
                /** @var \App\Entity\UserGroup $group */
                if ($this->isOwner($group, $user)) {
                    return true;
                }

                foreach ($group->getUserGroupRelations() as $relation) {
                    /** @var \App\Entity\UserGroupRelation $relation */
                    if ($relation->getMember() === $user) {
                        return $relation->isCanDelete();
                    }
                }
            }
        }

        return false;
    }
}
