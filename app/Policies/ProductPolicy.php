<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    use HandlesAuthorization;

    public function show(User $user, Product $product)
    {
        return $user->isOwnerOf($product);
    }

    public function edit(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && (
                $product->isDeclined()
                || $product->isNotApproved()
            )
        );
    }

    public function edit_variants(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && (
                $product->isApproved()
                || $product->isAutoApproved()
            )
        );
    }

    public function delete(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && (
                !($product->isSynced() && $product->isActive())
                || !$product->store->isInSync()
            )
        );
    }

    public function ignore(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && (
                $product->isApproved()
                || $product->isAutoApproved()
            )
        );
    }

    public function unignore(User $user, Product $product)
    {
        return $this->ignore($user, $product);
    }

    public function push_to_store(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && (
                $product->isApproved()
                || $product->isAutoApproved()
            )
            && $product->isDraft()
            && $product->store->isInSync() // push to store not used for custom stores
        );
    }

    public function push_to_store_without_moderation(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && !$product->wasDeclinedAtLeastOnce()
            && $product->isDraft()

            // prepaid categories will be sent to moderation anyways
            && !$product->template()->category->isPrepaid()
        );
    }

    public function send_to_moderation(User $user, Product $product)
    {
        return (
            $user->isOwnerOf($product)
            && $product->isNotApproved()
        );
    }

}
