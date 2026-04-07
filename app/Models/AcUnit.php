<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcUnit extends Model
{
    protected $fillable = [
        'rumah_sakit_id', 'gedung', 'jenis_ac', 'merk_ac',
        'kapasitas_pk', 'ruangan', 'lantai', 'frekuensi_cuci',
    ];

    public function rumahSakit()
    {
        return $this->belongsTo(RumahSakit::class);
    }
}
