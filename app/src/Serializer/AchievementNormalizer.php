<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Achievement;
use App\Entity\Tag;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class AchievementNormalizer extends AbstractEntityNormalizer
{
    /**
     * @param Achievement $object
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

        $tagsCallable = function (
            object $innerObject
        ): array {
            /** @var \Doctrine\Common\Collections\ArrayCollection $innerObject */
            return $innerObject->map(function (Tag $tag) {
                return $tag->getRawId();
            })->toArray();
        };

        $data = $this->normalizer->normalize(
            $object,
            $format,
            [
                AbstractNormalizer::CALLBACKS => [
                    'user' => $userCallable,
                    'tags' => $tagsCallable,
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'public',
                ],
            ]
        );

        $data['isPublic'] = $object->isPublic();

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Achievement;
    }
}
