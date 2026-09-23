<?php

namespace App\Jobs;

use App\Services\OperationalHeartbeat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordWorkerHeartbeat implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(public string $dispatchedAt) {}

    public function handle(OperationalHeartbeat $heartbeat): void
    {
        // Use enqueue time: a worker processing a stale backlog is not healthy.
        $heartbeat->record('worker', $this->dispatchedAt);
    }
}
