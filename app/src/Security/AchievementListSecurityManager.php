<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\AchievementList;
use App\Entity\User;

class AchievementListSecurityManager
{
    public function isOwner(
        AchievementList $object,
        User $user
    ): bool {
        return $object->getOwner() === $user;
    }

    public function canView(
        AchievementList $subject,
        User $user
    ): bool {
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

    public function canEdit(
        AchievementList $subject,
        User $user
    ): bool {
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
