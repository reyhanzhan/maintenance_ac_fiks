<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $fillable = ['rumah_sakit_id', 'nama'];

    public function rumahSakit()
    {
        return $this->belongsTo(RumahSakit::class);
    }

    public function serviceReports()
    {
        return $this->hasMany(ServiceReport::class);
    }
}
