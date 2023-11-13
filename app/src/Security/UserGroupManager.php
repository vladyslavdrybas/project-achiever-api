<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Entity\UserGroupPermissions;
use App\Entity\UserGroupRelation;
use App\Entity\UserGroupRelationType;
use App\Repository\UserGroupRelationRepository;
use App\Repository\UserGroupRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function method_exists;
use function strtoupper;
use function ucfirst;

class UserGroupManager
{
    public function __construct(
        protected readonly UserGroupRepository $userGroupRepository,
        protected readonly UserGroupRelationRepository $userGroupRelationRepository
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

    public function addMember(
        UserGroup $group,
        User $member,
        User $owner,
        string $role
    ): UserGroup {
        $role = UserGroupRelationType::getOrException($role);
        if ($member === $owner) {
            return $group;
        }

        foreach ($group->getUserGroupRelations() as $relation) {
            if ($relation->getMember() === $member) {
                throw new \Exception('User has been added to the group already.');
            }
        }

        $relation = new UserGroupRelation();
        $relation->setMember($member);
        $relation->setUserGroup($group);
        $relation->setTitle($role);

        $permissions = constant(sprintf(
            '%s::%s',
            UserGroupPermissions::class,
            strtoupper($role->value)
        ));

        foreach ($permissions as $key => $value) {
            $key = ucfirst($key);
            if (method_exists($relation, 'isCan' . $key)) {
                $relation->{'setCan' . $key}($value);
            }
        }

        $this->userGroupRelationRepository->add($relation);
        $this->userGroupRelationRepository->save();

        $group->addUserGroupRelation($relation);
        //TODO notify member;

        return $group;
    }

    public function removeMember(
        UserGroup $group,
        User $member,
        User $owner
    ): void {
        if ($member === $owner) {
            throw new \Exception('Owner can not remove himself from the group. Switch owner.');
        }

        foreach ($group->getUserGroupRelations() as $relation)
        {
            if ($relation->getMember() === $member) {
                $this->userGroupRelationRepository->remove($relation);
                $this->userGroupRelationRepository->save();
                break;
                //TODO notify member;
            }
        }
    }

    public function membersShow(
        UserGroup $group,
        int $offset = 0,
        int $limit = 5
    ): array {
        return $this->userGroupRelationRepository->findAllByGroup($group, $offset, $limit);
    }

    public function removeGroup(
        UserGroup $group,
        User $owner
    ): void {
        $batch = 0;
        foreach($group->getUserGroupRelations() as $relation)
        {
            $this->userGroupRelationRepository->remove($relation);
            $batch++;
            if ($batch > 10) {
                $this->userGroupRelationRepository->save();
            }
        }

        if ($batch > 0) {
            $this->userGroupRelationRepository->save();
        }

        $this->userGroupRepository->remove($group);
        $this->userGroupRepository->save();
    }

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

    public function canManageMembers(
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
