<?php

declare(strict_types=1);

namespace App\Transfer;

use App\Entity\Achievement;
use App\Entity\AchievementList;

class AchievementPrerequisiteRelationTransfer extends AbstractTransfer
{
    protected Achievement $achievement;
    protected ?AchievementList $achievementList;
    protected Achievement $prerequisite;
    protected ?AchievementList $prerequisiteList;
    protected string $condition = 'complete';
    protected int $priority = 0;
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

    /**
     * @return \App\Entity\AchievementList|null
     */
    public function getAchievementList(): ?AchievementList
    {
        return $this->achievementList;
    }

    /**
     * @param \App\Entity\AchievementList|null $achievementList
     */
    public function setAchievementList(?AchievementList $achievementList): void
    {
        $this->achievementList = $achievementList;
    }

    /**
     * @return \App\Entity\AchievementList|null
     */
    public function getPrerequisiteList(): ?AchievementList
    {
        return $this->prerequisiteList;
    }

    /**
     * @param \App\Entity\AchievementList|null $prerequisiteList
     */
    public function setPrerequisiteList(?AchievementList $prerequisiteList): void
    {
        $this->prerequisiteList = $prerequisiteList;
    }
}
