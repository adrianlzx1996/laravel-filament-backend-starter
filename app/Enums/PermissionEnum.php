<?php

namespace App\Enums;

enum PermissionEnum: string
{
    case VIEW_ANY = 'view-any';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE_ANY = 'delete-any';
    case DELETE = 'delete';
    case RESTORE_ANY = 'restore-any';
    case RESTORE = 'restore';
    case FORCE_DELETE_ANY = 'force-delete-any';
    case FORCE_DELETE = 'force-delete';
    case EXPORT = 'export';
    case IMPORT = 'import';
}
