<?php

declare(strict_types=1);

namespace App\Entity;

interface EntityInterface
{
    public const TIME_RANGE_OLDER = 0;
    public const TIME_RANGE_NEWER = 1;

    public function getObject(): string;
    public function getRawId(): string;
}
