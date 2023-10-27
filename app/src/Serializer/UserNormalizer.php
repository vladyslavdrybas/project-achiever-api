<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly GetSetMethodNormalizer $normalizer,
    ) {
    }

    /**
     * @param User $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'achievements',
                    'roles',
                    'username',
                    'password',
                    'userIdentifier',
                    'emailVerified',
                    'active',
                    'banned',
                    'deleted',
                ],
            ]
        );

        $data['isActive'] = $object->isActive();
        $data['isEmailVerified'] = $object->isEmailVerified();
        $data['isBanned'] = $object->isBanned();
        $data['isDeleted'] = $object->isDeleted();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }
}
