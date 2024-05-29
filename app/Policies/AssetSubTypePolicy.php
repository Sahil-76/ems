<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetSubType;
use App\User;

class AssetSubTypePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","view");
    }

    public function create(User $user)
    {
        return $user->hasPermission("AssetSubType","create");
    }

    public function update(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","update");
    }

    public function delete(User $user, AssetSubType $assetSubType)
    {
        return $user->hasPermission("AssetSubType","delete");
    }

    public function restore(User $user, AssetSubType $assetSubType)
    {
        //
    }

    public function forceDelete(User $user, AssetSubType $assetSubType)
    {
        //
    }
}
