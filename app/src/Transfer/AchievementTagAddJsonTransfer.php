<?php

declare(strict_types=1);

namespace App\Transfer;

class AchievementTagAddJsonTransfer extends AbstractTransfer
{
    protected array $tags;

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
