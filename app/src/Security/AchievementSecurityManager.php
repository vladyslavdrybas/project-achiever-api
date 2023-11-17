<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Achievement;
use App\Entity\AchievementList;
use App\Entity\User;
use App\Repository\AchievementListRepository;

class AchievementSecurityManager
{
    public function __construct(
        protected readonly AchievementListRepository $achievementListRepository
    ) {}

    public function isOwner(
        Achievement $object,
        User $user
    ): bool {
        return $object->getOwner() === $user;
    }

    public function isAchievementPublicView(Achievement $subject, string $attribute): bool
    {
        return Permissions::VIEW === $attribute && $subject->isPublic();
    }

    public function isAchievementListPublicView(?AchievementList $subject, string $attribute): bool
    {
        return Permissions::VIEW === $attribute && $subject?->isPublic();
    }

    public function isUserHasPermissionInList(string $attribute, User $user, AchievementList $achievementList): bool
    {
        return $this->achievementListRepository->isUserHasPermission($achievementList, $user, $attribute);
    }
}
