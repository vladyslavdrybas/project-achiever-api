<?php

declare(strict_types=1);

namespace App\Transfer;

use Symfony\Component\Serializer\Annotation\Groups;

use function array_pop;
use function explode;

abstract class AbstractTransfer implements TransferInterface
{
    /**
     * @var string
     * @Groups({"base"})
     */
    protected string $object;

    /**
     * @return string
     */
    public function getObject(): string
    {
        $namespace = explode('\\', static::class);

        return array_pop($namespace);
    }
}
