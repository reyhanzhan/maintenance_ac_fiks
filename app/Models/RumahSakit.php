<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RumahSakit extends Model
{
    protected $fillable = ['nama', 'alamat', 'koordinator_lapangan', 'mengetahui_surat_jalan'];

    public function ruangans()
    {
        return $this->hasMany(Ruangan::class);
    }

    public function acUnits()
    {
        return $this->hasMany(AcUnit::class);
    }

    public function serviceReports()
    {
        return $this->hasMany(ServiceReport::class);
    }

    public function suratJalans()
    {
        return $this->hasMany(SuratJalan::class);
    }
}
