<?php

namespace App\Console\Commands;

use App\Models\ReportExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SecureLegacyExports extends Command
{
    protected $signature = 'growpos:secure-legacy-exports {--apply : Move legacy public exports into private quarantine}';

    protected $description = 'Audit and quarantine legacy public report files without deleting their private copies';

    public function handle(): int
    {
        $count = 0;
        foreach (ReportExport::whereNull('tenant_id')->whereNotNull('file_path')->lazyById(100) as $export) {
            $path = $export->file_path;
            if (! str_starts_with($path, 'exports/') || str_contains($path, '..') || ! Storage::disk('public')->exists($path)) {
                continue;
            }
            $count++;
            if ($this->option('apply')) {
                $private = 'legacy-export-quarantine/'.Str::uuid().'.xlsx';
                $stream = Storage::disk('public')->readStream($path);
                try {
                    if (! Storage::disk('local')->put($private, $stream) || Storage::disk('local')->size($private) !== Storage::disk('public')->size($path)) {
                        $this->error('Salinan privat gagal diverifikasi; berkas asal dipertahankan.');

                        return self::FAILURE;
                    }
                } finally {
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }
                if (! Storage::disk('public')->delete($path)) {
                    $this->error('Berkas publik tidak dapat dipindahkan.');

                    return self::FAILURE;
                }
                $export->update(['file_path' => $private, 'status' => 'failed']);
            }
        }
        $this->info($count.' berkas lama '.($this->option('apply') ? 'diamankan; ekspor ulang laporan dari toko yang benar.' : 'terdeteksi. Gunakan --apply untuk mengamankan.'));

        return self::SUCCESS;
    }
}
