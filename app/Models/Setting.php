<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\BelongsToTenant;

class Setting extends Model
{
    use HasFactory, BelongsToTenant;

    // Tambahkan baris ini
    protected $fillable = [
        'tenant_id',
        'key',
        'value',
    ];

    // Jika kamu ingin menghubungkan kembali ke model Tenant
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
}
