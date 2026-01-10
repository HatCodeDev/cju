<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Retiree;
use Illuminate\Auth\Access\HandlesAuthorization;

class RetireePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Retiree');
    }

    public function view(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('View:Retiree');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Retiree');
    }

    public function update(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('Update:Retiree');
    }

    public function delete(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('Delete:Retiree');
    }

    public function restore(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('Restore:Retiree');
    }

    public function forceDelete(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('ForceDelete:Retiree');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Retiree');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Retiree');
    }

    public function replicate(AuthUser $authUser, Retiree $retiree): bool
    {
        return $authUser->can('Replicate:Retiree');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Retiree');
    }

}