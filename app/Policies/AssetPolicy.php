<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\Asset;
use App\User;

class AssetPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        
    }

    public function view(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","view");
    }

    public function create(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","create");
    }

    public function update(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","update");
    }

    public function delete(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","delete");
    }

    public function restore(User $user, Asset $asset)
    {
        
    }

    public function forceDelete(User $user, Asset $asset)
    {
        //
    }
    
    public function assignEquipments(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignEquipments");
    }

    public function assignmentList(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignmentList");
    }

    public function assignAsset(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","assignAsset");
    }

    public function dashboard(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","dashboard");
    }
    public function modify(User $user, Asset $asset)
    {
        return $user->hasPermission("Asset","modify");
    }
}
