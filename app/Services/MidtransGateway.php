<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Transaction;

class MidtransGateway
{
    private function configure(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
    }

    public function charge(string $invoice, int $amount): ?string
    {
        $this->configure();
        $response = CoreApi::charge(['payment_type' => 'qris', 'transaction_details' => ['order_id' => $invoice, 'gross_amount' => $amount], 'qris' => ['acquirer' => 'gopay']]);

        return $response->actions[0]->url ?? null;
    }

    public function status(string $invoice): array
    {
        $this->configure();

        return (array) Transaction::status($invoice);
    }
}
