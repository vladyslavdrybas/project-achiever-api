<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\AchievementList;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AchievementListNormalizer extends AbstractEntityNormalizer
{
    /**
     * @param AchievementList $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $achievementsAmount = $object->getAchievements()->count();

        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                    'owner' => [$this, 'normalizeWithIdOnly'],
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'listGroupRelations',
                    'achievements',
                ]
            ]
        );

        $data['achievementsAmount'] = $achievementsAmount;

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof AchievementList;
    }
}
