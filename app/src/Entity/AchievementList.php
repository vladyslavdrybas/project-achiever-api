<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AchievementListRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: AchievementListRepository::class, readOnly: false)]
#[ORM\Table(name: "achievement_list")]
#[ORM\UniqueConstraint(
    name: 'owner_title_idx',
    columns: ['owner_id', 'title']
)]
class AchievementList extends AbstractEntity
{
    #[ORM\Column(name: "title", type: Types::STRING, length: 125, unique: false)]
    protected string $title;

    #[ORM\Column(name: "description", type: Types::STRING, length: 255, unique: false)]
    protected string $description;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'ownedUserGroups')]
    #[ORM\JoinColumn(name:'owner_id', referencedColumnName: 'id', nullable: false)]
    protected User $owner;

    #[ORM\JoinTable(name: 'achievement_list_relation')]
    #[ORM\JoinColumn(name: 'list_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'achievement_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Achievement::class, inversedBy: 'lists')]
    protected Collection $achievements;

    #[ORM\JoinTable(name: 'achievement_list_group_relation')]
    #[ORM\JoinColumn(name: 'list_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'user_group_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: UserGroup::class)]
    protected Collection $listGroupRelations;

    public function __construct()
    {
        parent::__construct();
        $this->achievements = new ArrayCollection();
        $this->listGroupRelations = new ArrayCollection();
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
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(Achievement $achievement): void
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements->add($achievement);
            $achievement->addList($this);
        }
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $achievements
     */
    public function setAchievements(Collection $achievements): void
    {
        foreach ($achievements as $achievement) {
            if (!$achievement instanceof Achievement) {
                throw new \Exception('Item should be instance of Achievement');
            }

            $this->addAchievement($achievement);
        }
        $this->achievements = $achievements;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getListGroupRelations(): Collection
    {
        return $this->listGroupRelations;
    }

    public function addGroup(UserGroup $group): void
    {
        if (!$this->listGroupRelations->contains($group)) {
            $this->listGroupRelations->add($group);
            $group->addList($this);
        }
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $listGroupRelations
     */
    public function setListGroupRelations(Collection $listGroupRelations): void
    {
        foreach ($listGroupRelations as $relation) {
            if (!$relation instanceof UserGroup) {
                throw new \Exception('Item should be instance of UserGroup');
            }

            $this->addGroup($relation);
        }
    }
}
