<?php

declare(strict_types=1);

namespace App\Serializer;

use DateTimeInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DateTimeInterfaceNormalizer implements NormalizerInterface
{
    /**
     * @param DateTimeInterface $object
     * @param string|null $format
     * @param array $context
     * @return string
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->format(\DateTimeInterface::W3C);
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof DateTimeInterface;
    }
}
