<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\FirebaseCloudMessaging;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class FcmTokenNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly GetSetMethodNormalizer $normalizer,
    ) {
    }
    /**
     * @param \App\Entity\FirebaseCloudMessaging $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $userCallable = function (
            object $innerObject
        ): array {
            /** @var \App\Entity\User $innerObject */
            return [
                'id' => $innerObject->getRawId(),
            ];
        };

        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                    'user' => $userCallable,
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'id',
                    'rawId',
                    'token',
                    'object',
                    'createdAt',
                    'updatedAt',
                ],
            ]
        );


        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof FirebaseCloudMessaging;
    }
}
