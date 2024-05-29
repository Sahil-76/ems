<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetCategory;
use App\User;

class AssetCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","view");
    }

    public function create(User $user)
    {
        return $user->hasPermission("AssetCategory","create");
    }

    public function update(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","update");
    }

    public function delete(User $user, AssetCategory $assetCategory)
    {
        return $user->hasPermission("AssetCategory","delete");
    }

    public function restore(User $user, AssetCategory $assetCategory)
    {
        //
    }

    public function forceDelete(User $user, AssetCategory $assetCategory)
    {
        //
    }
}
