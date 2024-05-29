<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetType;
use App\User;

class AssetTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        
    }

    public function view(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','view');
    }

    public function create(User $user)
    {
        return $user->hasPermission('AssetType','create');
    }

    public function update(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','update');
    }

    public function delete(User $user, AssetType $assetType)
    {
        return $user->hasPermission('AssetType','delete');
    }

    public function restore(User $user, AssetType $assetType)
    {
        //
    }

    public function forceDelete(User $user, AssetType $assetType)
    {
        //
    }
}
