<?php

declare(strict_types=1);

namespace App\Transfer;

interface TransferInterface
{
    public function getObject(): string;
}
