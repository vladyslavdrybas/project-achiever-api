<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AchievementPrerequisiteRelationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AchievementPrerequisiteRelationRepository::class, readOnly: false)]
#[ORM\Table(name: "achievement_prerequisite_relation")]
#[ORM\UniqueConstraint(
    name: 'achievement_prerequisite_idx',
    columns: ['achievement_id', 'prerequisite_id']
)]
class AchievementPrerequisiteRelation extends AbstractEntity
{
    #[ORM\ManyToOne(targetEntity: Achievement::class, inversedBy: 'meAchievementIn')]
    #[ORM\JoinColumn(name:'achievement_id', referencedColumnName: 'id', nullable: false)]
    protected Achievement $achievement;

    #[ORM\ManyToOne(targetEntity: Achievement::class, inversedBy: 'mePrerequisiteIn')]
    #[ORM\JoinColumn(name:'prerequisite_id', referencedColumnName: 'id', nullable: false)]
    protected Achievement $prerequisite;

    /**
     * should be done to start achievement by prerequisite.
     * @var string
     */
    #[ORM\Column(name: "condition", type: Types::STRING, options: ['default' => 'complete'])]
    protected string $condition = 'complete';

    /**
     * order of prerequisite. what should be done first.
     * @var int
     */
    #[ORM\Column(name: "priority", type: Types::INTEGER, options: ['default' => 0])]
    protected int $priority = 0;

    /**
     * should I finish it before start achievement?
     * @var bool
     */
    #[ORM\Column(name: "is_required", type: Types::BOOLEAN, options: ['default' => false])]
    protected bool $isRequired = false;

    /**
     * @return \App\Entity\Achievement
     */
    public function getAchievement(): Achievement
    {
        return $this->achievement;
    }

    /**
     * @param \App\Entity\Achievement $achievement
     */
    public function setAchievement(Achievement $achievement): void
    {
        $this->achievement = $achievement;
    }

    /**
     * @return \App\Entity\Achievement
     */
    public function getPrerequisite(): Achievement
    {
        return $this->prerequisite;
    }

    /**
     * @param \App\Entity\Achievement $prerequisite
     */
    public function setPrerequisite(Achievement $prerequisite): void
    {
        $this->prerequisite = $prerequisite;
    }

    /**
     * @return string
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority(int $priority): void
    {
        $this->priority = $priority;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->isRequired;
    }

    /**
     * @param bool $isRequired
     */
    public function setIsRequired(bool $isRequired): void
    {
        $this->isRequired = $isRequired;
    }
}
