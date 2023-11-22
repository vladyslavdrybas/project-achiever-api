<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use function array_key_exists;
use function in_array;
use function var_dump;

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
        $ignoredAttributes = array_merge(
            $context[AbstractNormalizer::IGNORED_ATTRIBUTES] ?? [],
            [
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
            ]
        );

        $customAttributes = $context['custom_attributes'] ?? [];

        $context = [
            AbstractNormalizer::CALLBACKS => [
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => $ignoredAttributes,
        ];

        $data = $this->normalizer->normalize(
            $object,
            $format,
            $context
        );

        if (!in_array('isActive', $ignoredAttributes)) {
            $data['isActive'] = $object->isActive();
        }

        if (!in_array('isEmailVerified', $ignoredAttributes)) {
            $data['isEmailVerified'] = $object->isEmailVerified();
        }

        if (!in_array('isBanned', $ignoredAttributes)) {
            $data['isBanned'] = $object->isBanned();
        }

        if (!in_array('isDeleted', $ignoredAttributes)) {
            $data['isDeleted'] = $object->isDeleted();
        }

        if (in_array('achievementsAmount', $customAttributes)) {
            $data['achievementsAmount'] = $object->getAchievements()->count();
        }

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }
}
