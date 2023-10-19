<?php

declare(strict_types=1);

namespace App\Transfer;

use DateTimeInterface;

class AchievementCreateJsonTransfer extends AbstractTransfer
{
    protected string $title;
    protected string $description;
    protected bool $isPublic = false;
    protected ?DateTimeInterface $doneAt;
    protected array $tags;

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
     * @return \DateTimeInterface|null
     */
    public function getDoneAt(): ?DateTimeInterface
    {
        return $this->doneAt;
    }

    /**
     * @param \DateTimeInterface|null $doneAt
     */
    public function setDoneAt(?DateTimeInterface $doneAt = null): void
    {
        $this->doneAt = $doneAt;
    }

    /**
     * @return array
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }
}
