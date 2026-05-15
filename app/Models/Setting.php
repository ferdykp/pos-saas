<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
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
