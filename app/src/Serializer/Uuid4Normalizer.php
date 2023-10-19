<?php

declare(strict_types=1);

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Uid\UuidV4;

class Uuid4Normalizer implements NormalizerInterface
{
    /**
     * @param UuidV4 $object
     * @param string|null $format
     * @param array $context
     */
    public function normalize($object, string $format = null, array $context = []): string
    {
        return $object->toRfc4122();
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof UuidV4;
    }
}
