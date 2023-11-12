<?php

namespace App\Entity;

interface UserGroupPermissions {
    const VIEWER = [
        'veiw' => true,
        'edit' => false,
        'delete' => false,
        'manage' => false,
    ];

    const EDITOR = [
        'veiw' => true,
        'edit' => true,
        'delete' => false,
        'manage' => false,
    ];

    const MANAGER = [
        'veiw' => true,
        'edit' => true,
        'delete' => false,
        'manage' => true,
    ];

    const OWNER = [
        'veiw' => true,
        'edit' => true,
        'delete' => true,
        'manage' => true,
    ];
}
