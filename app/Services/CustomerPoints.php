<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Setting;

class CustomerPoints
{
    // Caller holds the order lock and calls this only on the first paid transition.
    public function award(Order $order): void
    {
        if (! $order->customer_id) {
            return;
        }
        $customer = Customer::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->lockForUpdate()->find($order->customer_id);
        if (! $customer) {
            return;
        }
        $settings = Setting::withoutGlobalScopes()->where('tenant_id', $order->tenant_id)->pluck('value', 'key');
        $rule = (float) ($settings['point_rule_value'] ?? 0);
        if ($rule <= 0 || (filter_var($settings['point_member_only'] ?? false, FILTER_VALIDATE_BOOLEAN) && ! $customer->is_member)) {
            return;
        }
        $points = match ($settings['point_mode'] ?? 'disabled') {
            'per_investment' => floor($order->grand_total / $rule),
            'flat' => floor($rule),
            'percentage' => floor($order->grand_total * $rule / 100),
            default => 0,
        };
        if ($points > 0) {
            $customer->increment('points', $points);
            $order->update(['points_awarded' => $points]);
        }
    }
}
