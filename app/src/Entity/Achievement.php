<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AchievementRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;
use function sprintf;

#[ORM\Entity(repositoryClass: AchievementRepository::class, readOnly: false)]
#[ORM\Table(name: "achievement")]
class Achievement extends AbstractEntity
{
    #[ORM\Column(name: "title", type: Types::STRING, length: 125, unique: false)]
    protected string $title;

    #[ORM\Column(name: "description", type: Types::STRING, length: 255, unique: false)]
    protected string $description;

    #[ORM\Column(name: "is_public", type: Types::BOOLEAN, options: ["default" => false])]
    protected bool $isPublic = false;

    #[ORM\Column(
        name: "done_at",
        type: Types::DATETIME_IMMUTABLE,
        nullable: false
    )]
    protected DateTimeImmutable $doneAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'achievements')]
    #[ORM\JoinColumn(name:'user_id', referencedColumnName: 'id', nullable: false)]
    protected User $user;

    #[ORM\JoinTable(name: 'achievement_tag')]
    #[ORM\JoinColumn(name: 'achievement_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    protected Collection $tags;

    public function __construct()
    {
        parent::__construct();
        $this->tags = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection
     */
    public function getTags(): ArrayCollection|Collection
    {
        return $this->tags;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection $tags
     */
    public function setTags(ArrayCollection|Collection $tags): void
    {
        $this->tags = $tags;
    }

    /**
     * @param \App\Entity\Tag $tag
     * @return void
     * @throws \Exception
     */
    public function addTag(Tag $tag): void
    {
        if ($this->tags->contains($tag)) {
            throw new Exception(sprintf("%s with id % exists.", Tag::class, $tag->getId()));
        }

        $this->tags->add($tag);
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
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    /**
     * @param bool $isPublic
     */
    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDoneAt(): DateTimeImmutable
    {
        return $this->doneAt;
    }

    /**
     * @param \DateTimeImmutable $doneAt
     */
    public function setDoneAt(DateTimeImmutable $doneAt): void
    {
        $this->doneAt = $doneAt;
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
