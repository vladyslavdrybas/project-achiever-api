<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\AchievementList;
use App\Entity\User;

class AchievementListBuilder implements IEntityBuilder
{
    public function baseAchievementList(
        string $title,
        string $description,
        User $owner,
        bool $isPublic = false
    ): AchievementList {
        $list = new AchievementList();
        $list->setTitle($title);
        $list->setDescription($description);
        $list->setOwner($owner);
        $list->setIsPublic($isPublic);

        return $list;
    }
}
