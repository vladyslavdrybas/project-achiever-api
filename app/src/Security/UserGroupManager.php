<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupRelation;
use App\Entity\UserGroupRelationType;
use App\Repository\UserGroupRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserGroupManager
{
    public function __construct(
        protected readonly UserGroupRepository $userGroupRepository
    ) {}

    public function createGroup(
        string $title,
        string $description,
        User $owner
    ): UserGroup {
        $group = $this->userGroupRepository->findOneBy([
            'owner' => $owner,
            'title' => $title,
        ]);

        if ($group instanceof UserGroup) {
            throw new \Exception('User group already exists.');
        }

        $group = new UserGroup();
        $group->setTitle($title);
        $group->setDescription($description);
        $group->setOwner($owner);

        $this->userGroupRepository->add($group);
        $this->userGroupRepository->save();

        return $group;
    }

    public function editGroup(
        string $title,
        string $description,
        UserGroup $group,
        User $user
    ): UserGroup {
        if (!$this->canEdit($group, $user)) {
            throw new AccessDeniedException();
        }

        $group->setTitle($title);
        $group->setDescription($description);

        $this->userGroupRepository->add($group);
        $this->userGroupRepository->save();

        return $group;
    }

    public function addUserToGroup(User $user, UserGroup $group, string $role): void
    {
        $role = UserGroupRelationType::from($role);
        var_dump($role);
        $relation = new UserGroupRelation();
    }

    public function removeUserFromGroup() {}
    public function removeGroup() {}

    public function isOwner(UserGroup $object, User $user): bool
    {
        return $object->getOwner() === $user;
    }

    public function canView(UserGroup $object, User $user): bool
    {
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

    public function canEdit(UserGroup $object, User $user): bool
    {
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

    public function canDelete(UserGroup $object, User $user): bool
    {
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

    public function canManageMembers(UserGroup $object, User $user): bool
    {
        if ($this->isOwner($object, $user)) {
            return true;
        }

        foreach ($object->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $user) {
                return $relation->isCanManageMembers();
            }
        }

        return false;
    }
}
