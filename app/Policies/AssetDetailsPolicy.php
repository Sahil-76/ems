<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\AssetDetails;
use App\User;

class AssetDetailsPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        //
    }

    public function view(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","view");
    }

    public function create(User $user)
    {
        return $user->hasPermission("AssetDetails","create");
    }

    public function update(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","update");
    }

    public function delete(User $user, AssetDetails $assetDetail)
    {
        return $user->hasPermission("AssetDetails","delete");
    }

    public function restore(User $user, AssetDetails $assetDetail)
    {
        //
    }

    public function forceDelete(User $user, AssetDetails $assetDetail)
    {
        //
    }
}
