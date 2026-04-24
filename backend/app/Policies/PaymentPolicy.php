<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function view(User $user, Payment $payment): bool
    {
        return $user->id === $payment->order->user_id || $user->isAdmin() || $user->isStaff();
    }

    public function refund(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }
}
