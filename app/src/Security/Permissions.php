<?php

declare(strict_types=1);

namespace App\Security;

interface Permissions
{
    const READ = 'read';
    const UPDATE = 'update';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const MANAGE_MEMBERS = 'manage_members';
}
