<?php

namespace App\Policies;

use Gate;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Components\Money;
use App\Models\User;
use App\Models\ProductVariant;

class ProductVariantPolicy
{
    use HandlesAuthorization;

    public function show(User $user, ProductVariant $variant)
    {
        return $user->isOwnerOf($variant);
    }

    public function edit(User $user, ProductVariant $variant)
    {
        return (
            $user->isOwnerOf($variant)
            && Gate::allows('edit', $variant->product)
        );
    }

    public function ignore(User $user, ProductVariant $variant)
    {
        return $this->edit($user, $variant);
    }

    public function unignore(User $user, ProductVariant $variant)
    {
        return $this->edit($user, $variant);
    }

    public function attach_to_order(User $user, ProductVariant $variant)
    {
        $product = $variant->product;
        return (
            $variant->product
            && $user->isOwnerOf($product)
            && (
                $product->isApproved()
                || $product->isAutoApproved()
            )
        );
    }

    public function purchase(User $user, ProductVariant $variant)
    {
        return (
            $variant->model
            && $variant->model->template
            && $variant->modelPriceIsSet()
        );
    }
}
