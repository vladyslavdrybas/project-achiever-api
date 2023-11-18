<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserNormalizer extends AbstractEntityNormalizer
{

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
                    'password',
                    'userIdentifier',
                    'emailVerified',
                    'active',
                    'banned',
                    'deleted',
                    'firebaseCloudMessagingTokens',
                    'ownedUserGroups',
                    'userGroupRelations',
                    'memberOfUserGroups',
                    'createdAt',
                    'updatedAt',
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
