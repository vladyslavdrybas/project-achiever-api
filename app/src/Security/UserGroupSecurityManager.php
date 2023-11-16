<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Repository\UserGroupRelationRepository;
use App\Repository\UserGroupRepository;

class UserGroupSecurityManager
{
    public function __construct(
        protected readonly UserGroupRepository $userGroupRepository,
        protected readonly UserGroupRelationRepository $userGroupRelationRepository
    ) {}

    public function isOwner(
        UserGroup $object,
        User $user
    ): bool {
        return $object->getOwner() === $user;
    }

    public function canView(
        UserGroup $object,
        User $user
    ): bool {
        foreach ($object->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $user) {
                return $relation->isCanView();
            }
        }

        if ($this->canEdit($object, $user)) {
            return true;
        }

        return false;
    }

    public function canEdit(
        UserGroup $object,
        User $user
    ): bool {
        if ($this->isOwner($object, $user)) {
            return true;
        }

        foreach ($object->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $user) {
                return $relation->isCanEdit();
            }
        }

        return false;
    }

    public function canDelete(
        UserGroup $object,
        User $user
    ): bool {
        if ($this->isOwner($object, $user)) {
            return true;
        }

        foreach ($object->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $user) {
                return $relation->isCanDelete();
            }
        }

        return false;
    }

    public function canManage(
        UserGroup $object,
        User $user
    ): bool {
        if ($this->isOwner($object, $user)) {
            return true;
        }

        foreach ($object->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $user) {
                return $relation->isCanManage();
            }
        }

        return false;
    }
}
