<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserGroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupRepository::class, readOnly: false)]
#[ORM\Table(name: "user_group")]
#[ORM\UniqueConstraint(
    name: 'owner_group_title_idx',
    columns: ['owner_id', 'title']
)]
class UserGroup extends AbstractEntity
{
    #[ORM\Column(name: "title", type: Types::STRING, length: 125, unique: false)]
    protected string $title = 'personal';

    #[ORM\Column(name: "description", type: Types::STRING, length: 255, unique: false)]
    protected string $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedUserGroups')]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\OneToMany(mappedBy: 'userGroup',targetEntity: UserGroupRelation::class)]
    protected Collection $userGroupRelations;

    public function __construct()
    {
        parent::__construct();
        $this->userGroupRelations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return \App\Entity\User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param \App\Entity\User $owner
     */
    public function setOwner(User $owner): void
    {
        $this->owner = $owner;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUserGroupRelations(): Collection
    {
        return $this->userGroupRelations;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $userGroupRelations
     */
    public function setUserGroupRelations(Collection $userGroupRelations): void
    {
        foreach ($userGroupRelations as $relation) {
            if (!$relation instanceof UserGroupRelation) {
                throw new \Exception('Member should be instance of UserGroupRelation');
            }

            $this->addUserGroupRelation($relation);
        }
    }

    public function addUserGroupRelation(UserGroupRelation $relation): void
    {
        if (!$this->userGroupRelations->contains($relation)) {
            $this->userGroupRelations->add($relation);
        } else {
            $relation = $this->userGroupRelations->get($this->userGroupRelations->indexOf($relation));
        }

        $relation->setUserGroup($this);
    }
}
