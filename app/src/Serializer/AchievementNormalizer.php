<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Achievement;
use App\Entity\Tag;
use DateTimeInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\UidNormalizer;
use Symfony\Component\Uid\UuidV4;
use function var_dump;

class AchievementNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly GetSetMethodNormalizer $normalizer,
    ) {
    }
    /**
     * @param Achievement $object
     * @param string|null $format
     * @param array $context
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $dateCallback = function (
            ?object $innerObject
        ): ?string {
            if (null === $innerObject) {
                return null;
            }
            if (!$innerObject instanceof \DateTime
                && !$innerObject instanceof \DateTimeImmutable
            ) {
                return '';
            }

            return $innerObject->format(\DateTimeInterface::W3C);
        };

        $idCallable = function (
            object $innerObject
        ): string {
            return $innerObject instanceof UuidV4 ? $innerObject->toRfc4122() : (string) $innerObject;
        };

        $userCallable = function (
            object $innerObject
        ): string {
            return $innerObject->getRawId();
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
                'id' => $idCallable,
                'user' => $userCallable,
                'doneAt' => $dateCallback,
                'createdAt' => $dateCallback,
                'updatedAt' => $dateCallback,
                'tags' => $tagsCallable,
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawId',
                    'public',
                    'user',
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
