<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportItem extends Model
{
    protected $fillable = [
        'service_report_id',
        'nomor',
        'nama_pemeriksaan',
        'is_normal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'is_normal' => 'boolean',
        ];
    }

    public function serviceReport()
    {
        return $this->belongsTo(ServiceReport::class);
    }

    public function photos()
    {
        return $this->hasMany(ServiceReportPhoto::class);
    }
}
