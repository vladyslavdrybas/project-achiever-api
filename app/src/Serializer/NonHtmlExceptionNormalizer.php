<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class NonHtmlExceptionNormalizer implements NormalizerInterface
{
    public function normalize($exception, string $format = null, array $context = []): array
    {
        return [
            'message' => $exception->getMessage(),
            'status' => $exception->getStatusCode(),
        ];
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FlattenException;
    }
}
