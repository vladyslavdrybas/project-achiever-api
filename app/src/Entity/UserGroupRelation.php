<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupRepository::class, readOnly: false)]
#[ORM\Table(name: "user_group_relation")]
#[ORM\UniqueConstraint(
    name: 'member_group_idx',
    columns: ['user_id', 'user_group_id']
)]
class UserGroupRelation extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'userGroupRelations')]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $member;

    #[ORM\ManyToOne(targetEntity: UserGroup::class, inversedBy: 'userGroupRelations')]
    #[ORM\JoinColumn(name:'user_group_id', referencedColumnName: 'id', nullable: false)]
    protected UserGroup $userGroup;

    #[ORM\Column(name: "title", type: Types::STRING, length: 10, unique: false, enumType: UserGroupRelationType::class)]
    protected UserGroupRelationType $title = UserGroupRelationType::VIEWER;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => true])]
    protected bool $canView = true;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $canEdit = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $canDelete = false;

    #[ORM\Column(type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $canManageMembers = false;

    /**
     * @return \App\Entity\User
     */
    public function getMember(): User
    {
        return $this->member;
    }

    /**
     * @param \App\Entity\User $member
     */
    public function setMember(User $member): void
    {
        $this->member = $member;
    }

    /**
     * @return \App\Entity\UserGroup
     */
    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    /**
     * @param \App\Entity\UserGroup $userGroup
     */
    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    /**
     * @return \App\Entity\UserGroupRelationType
     */
    public function getTitle(): UserGroupRelationType
    {
        return $this->title;
    }

    /**
     * @param \App\Entity\UserGroupRelationType $title
     */
    public function setTitle(UserGroupRelationType $title): void
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function isCanView(): bool
    {
        return $this->canView;
    }

    /**
     * @param bool $canView
     */
    public function setCanView(bool $canView): void
    {
        $this->canView = $canView;
    }

    /**
     * @return bool
     */
    public function isCanEdit(): bool
    {
        return $this->canEdit;
    }

    /**
     * @param bool $canEdit
     */
    public function setCanEdit(bool $canEdit): void
    {
        $this->canEdit = $canEdit;
    }

    /**
     * @return bool
     */
    public function isCanDelete(): bool
    {
        return $this->canDelete;
    }

    /**
     * @param bool $canDelete
     */
    public function setCanDelete(bool $canDelete): void
    {
        $this->canDelete = $canDelete;
    }

    /**
     * @return bool
     */
    public function isCanManageMembers(): bool
    {
        return $this->canManageMembers;
    }

    /**
     * @param bool $canManageMembers
     */
    public function setCanManageMembers(bool $canManageMembers): void
    {
        $this->canManageMembers = $canManageMembers;
    }
}
