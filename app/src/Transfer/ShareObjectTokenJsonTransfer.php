<?php

declare(strict_types=1);

namespace App\Transfer;

use DateTimeInterface;

class ShareObjectTokenJsonTransfer extends AbstractTransfer
{
    protected ?string $id = null;
    protected ?string $target = null;
    protected ?string $targetId = null;
    protected ?string $ownerId = null;
    protected ?DateTimeInterface $expireAt = null;
    protected ?string $achievementListId = null;
    protected bool $canEdit = false;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getTarget(): ?string
    {
        return $this->target;
    }

    /**
     * @param string|null $target
     */
    public function setTarget(?string $target): void
    {
        $this->target = $target;
    }

    /**
     * @return string|null
     */
    public function getTargetId(): ?string
    {
        return $this->targetId;
    }

    /**
     * @param string|null $targetId
     */
    public function setTargetId(?string $targetId): void
    {
        $this->targetId = $targetId;
    }

    /**
     * @return string|null
     */
    public function getOwnerId(): ?string
    {
        return $this->ownerId;
    }

    /**
     * @param string|null $ownerId
     */
    public function setOwnerId(?string $ownerId): void
    {
        $this->ownerId = $ownerId;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpireAt(): ?DateTimeInterface
    {
        return $this->expireAt;
    }

    /**
     * @param \DateTimeInterface|null $expireAt
     */
    public function setExpireAt(?DateTimeInterface $expireAt): void
    {
        $this->expireAt = $expireAt;
    }

    /**
     * @return string|null
     */
    public function getAchievementListId(): ?string
    {
        return $this->achievementListId;
    }

    /**
     * @param string|null $achievementListId
     */
    public function setAchievementListId(?string $achievementListId): void
    {
        $this->achievementListId = $achievementListId;
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
}
