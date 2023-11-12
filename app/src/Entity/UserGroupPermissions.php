<?php

namespace App\Entity;

interface UserGroupPermissions {
    const ROLES = [
        'viewer' => self::VIEWER,
        'editor' => self::EDITOR,
        'manager' => self::MANAGER,
        'owner' => self::OWNER,
    ];

    const VIEWER = [
        'view' => true,
        'edit' => false,
        'delete' => false,
        'manage' => false,
    ];

    const EDITOR = [
        'view' => true,
        'edit' => true,
        'delete' => false,
        'manage' => false,
    ];

    const MANAGER = [
        'view' => true,
        'edit' => true,
        'delete' => false,
        'manage' => true,
    ];

    const OWNER = [
        'view' => true,
        'edit' => true,
        'delete' => true,
        'manage' => true,
    ];
}
