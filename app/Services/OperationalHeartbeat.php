<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OperationalHeartbeat
{
    public function record(string $name, ?string $observedAt = null): void
    {
        $observedAt ??= now()->toDateTimeString();
        DB::table('operational_heartbeats')->insertOrIgnore(['name' => $name, 'observed_at' => $observedAt]);
        // A delayed job must never move an already newer heartbeat backwards.
        DB::table('operational_heartbeats')->where('name', $name)->where('observed_at', '<', $observedAt)->update(['observed_at' => $observedAt]);
    }

    public function fresh(string $name, int $minutes): bool
    {
        if (! Schema::hasTable('operational_heartbeats')) {
            return false;
        }
        $value = DB::table('operational_heartbeats')->where('name', $name)->value('observed_at');
        if (! $value) {
            return false;
        }
        $date = CarbonImmutable::parse($value);

        return $date->betweenIncluded(now()->subMinutes($minutes), now()->addMinute());
    }
}
