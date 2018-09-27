<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Store;

class StorePolicy
{
    use HandlesAuthorization;
    
    public function listOwn(User $user, Store $store)
    {
        return (bool)$user->id;
    }
    
    public function create(User $user, Store $store)
    {
        return (bool)$user->id;
    }
    
    public function show(User $user, Store $store)
    {
        return $user->isOwnerOf($store);
    }
    
    public function edit(User $user, Store $store)
    {
        return $user->isOwnerOf($store);
    }
    
    public function reload(User $user, Store $store)
    {
        return (
            $user->isOwnerOf($store)
            && $store->isInSync()
        );
    }
    
    public function delete(User $user, Store $store)
    {
        return $user->isOwnerOf($store);
    }
}
