<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CustomCode;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomCodePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CustomCode');
    }

    public function view(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('View:CustomCode');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CustomCode');
    }

    public function update(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('Update:CustomCode');
    }

    public function delete(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('Delete:CustomCode');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CustomCode');
    }

    public function restore(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('Restore:CustomCode');
    }

    public function forceDelete(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('ForceDelete:CustomCode');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CustomCode');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CustomCode');
    }

    public function replicate(AuthUser $authUser, CustomCode $customCode): bool
    {
        return $authUser->can('Replicate:CustomCode');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CustomCode');
    }

}