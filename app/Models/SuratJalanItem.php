<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuratJalanItem extends Model
{
    protected $fillable = [
        'surat_jalan_id',
        'banyaknya',
        'nama_ruangan',
    ];

    public function suratJalan()
    {
        return $this->belongsTo(SuratJalan::class);
    }
}
