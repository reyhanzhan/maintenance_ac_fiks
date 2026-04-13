<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalan extends Model
{
    protected $fillable = [
        'nomor',
        'rumah_sakit_id',
        'departemen',
        'deskripsi_pekerjaan',
        'tanggal',
        'penerima',
        'mengetahui',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function rumahSakit()
    {
        return $this->belongsTo(RumahSakit::class);
    }

    public function items()
    {
        return $this->hasMany(SuratJalanItem::class);
    }
}
