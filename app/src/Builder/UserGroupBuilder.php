<?php

declare(strict_types=1);

namespace App\Builder;

use App\Entity\User;
use App\Entity\UserGroup;
use App\Security\UserGroupManager;

class UserGroupBuilder implements IEntityBuilder
{
    public function __construct(
        protected readonly UserGroupManager $groupManager
    ) {}

    public function baseUserGroup(
        string $title,
        string $description,
        User $owner
    ): UserGroup {
        return $this->groupManager->createGroup($title, $description, $owner);
    }
}
