<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\UserGroup;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserGroupNormalizer extends AbstractEntityNormalizer
{
    /**
     * @param UserGroup $object
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
                    'owner' => [$this, 'normalizeWithIdOnly'],
                    'lists' => [$this, 'normalizeAchievementListInObject'],
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'userGroupRelations',
                    'rawId'
                ],
            ]
        );

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof UserGroup;
    }
}
