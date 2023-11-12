<?php

namespace App\Entity;

enum UserGroupRelationType: string
{
    case VIEWER = 'viewer'; // read only
    case EDITOR = 'editor'; // read and edit
    case MANAGER = 'manager'; // read, edit, manage users
    case OWNER = 'owner'; // full access

    public static function getOrDefault(string $value): UserGroupRelationType
    {
        $value = self::tryFrom($value);
        if (null === $value) {
            $value = self::VIEWER;
        }

        return $value;
    }

    public static function getOrException(int|string $value): static
    {
        $value = self::tryFrom($value);
        if (null === $value) {
            throw new \Exception('Not existed user group role.');
        }

        return $value;
    }
}
