<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportExport extends Model
{
    protected $fillable = ['user_id', 'report_type', 'start_date', 'end_date', 'status', 'file_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
