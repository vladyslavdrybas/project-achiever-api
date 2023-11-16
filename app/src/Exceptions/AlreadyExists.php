<?php

declare(strict_types=1);

namespace App\Exceptions;

use Throwable;

class AlreadyExists extends \Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        if (empty($message)) {
            $message = 'Already exists.';
        }

        parent::__construct($message, $code, $previous);
    }
}
