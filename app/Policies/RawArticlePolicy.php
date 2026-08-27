<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\RawArticle;
use Illuminate\Auth\Access\HandlesAuthorization;

class RawArticlePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:RawArticle');
    }

    public function view(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('View:RawArticle');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:RawArticle');
    }

    public function update(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('Update:RawArticle');
    }

    public function delete(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('Delete:RawArticle');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:RawArticle');
    }

    public function restore(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('Restore:RawArticle');
    }

    public function forceDelete(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('ForceDelete:RawArticle');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:RawArticle');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:RawArticle');
    }

    public function replicate(AuthUser $authUser, RawArticle $rawArticle): bool
    {
        return $authUser->can('Replicate:RawArticle');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:RawArticle');
    }

}