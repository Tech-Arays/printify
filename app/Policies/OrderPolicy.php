<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

use App\Components\Money;
use App\Models\User;
use App\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function add(User $user, Order $order)
    {
        // TODO: maybe use is_confirmed ?
        return (
            $user->id
            && !empty($order->store)
        );
    }

    public function show(User $user, Order $order)
    {
        return (
            (
                $user->isOwnerOf($order)
                || $user->isAdmin()
            )
            && $order->store
        );
    }

    public function edit(User $user, Order $order)
    {
        return $this->show($user, $order);
    }

    public function edit_shipping_info(User $user, Order $order)
    {
        return (
            $this->show($user, $order)
            && !$order->isPlaced()
            && $order->areAllShippingGroupsAssigned()
        );
    }

    public function edit_variants(User $user, Order $order)
    {
        return (
            $this->edit($user, $order)
            && $order->isDirectOrder()
            && !$order->isPlaced()
        );
    }

    public function cancel(User $user, Order $order)
    {
        return (
            $this->edit($user, $order)
            && !$order->isPlaced()
            && !$order->isCancelled()
            && !$order->isRefundRequested()
        );
    }

    public function restore(User $user, Order $order)
    {
        return (
            $this->edit($user, $order)
            && $order->isCancelled()
        );
    }

    public function refund(User $user, Order $order)
    {
        return (
            $this->edit($user, $order)
            && $order->isPlaced()
            && !$order->isRefunded()
            && !$order->isRefundRequested()
            && !$order->isFulfilled()
        );
    }

    public function delete(User $user, Order $order)
    {
        return (
            $user->isOwnerOf($order)
            && !$order->isPlaced()
            && !$order->isRefundRequested()
        );
    }

    public function pay(User $user, Order $order)
    {
        return (
            $user->isOwnerOf($order)
            && $user->hasPaymentMethod()
            && !$order->variants->isEmpty()
            && $order->shipping_method
            && $order->areAllShippingGroupsAssigned()
            && $order->areAllPricesSet()
            && Money::i()->amount($order->total()) > 0
            && !$order->isPaid()
            && !$order->isCancelled()
            && !$order->isRefunded()
        );
    }

    public function pull(User $user, Order $order)
    {
        return (
            $user->isAdmin()
        );
    }
}
