<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\AchievementList;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function method_exists;

abstract class AbstractEntityNormalizer implements NormalizerInterface
{
    public function __construct(
        protected readonly GetSetMethodNormalizer $normalizer,
    ) {
    }

    public function normalizeWithIdOnly(object $innerObject): array
    {
        /** @var \App\Entity\EntityInterface $innerObject */
        return [
            'id' => $innerObject->getRawId(),
        ];
    }

    public function normalizeUserInList(object $innerObject): array
    {
        /** @var \App\Entity\User $innerObject */
        return [
            'id' => $innerObject->getRawId(),
            'email' => $innerObject->getEmail(),
            'isActive' => $innerObject->isActive(),
            'isBanned' => $innerObject->isBanned(),
            'isDeleted' => $innerObject->isDeleted(),
        ];
    }

    public function normalizeAchievementListInObject(object $innerObject): array
    {
        $lists = [];

        if ($innerObject instanceof Collection) {
            foreach ($innerObject as $list) {
                if ($list instanceof AchievementList) {
                    $lists[] = [
                        'id' => $list->getRawId(),
                        'title' => $list->getTitle(),
                    ];
                }
            }
        } else if (method_exists($innerObject, 'getLists')) {
            foreach ($innerObject->getLists() as $list) {
                if ($list instanceof AchievementList) {
                    $lists[] = [
                        'id' => $list->getRawId(),
                        'title' => $list->getTitle(),
                    ];
                }
            }
        }

        return $lists;
    }
}
