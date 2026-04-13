<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReport extends Model
{
    protected $fillable = [
        'user_id',
        'rumah_sakit_id',
        'ruangan_id',
        'gedung',
        'merk_ac',
        'type_ac',
        'tanggal_service',
        'saran',
        'nama_penerima',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_service' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rumahSakit()
    {
        return $this->belongsTo(RumahSakit::class);
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class);
    }

    public function items()
    {
        return $this->hasMany(ServiceReportItem::class)->orderBy('nomor');
    }

    public function photos()
    {
        return $this->hasMany(ServiceReportPhoto::class);
    }

    public function generalPhotos()
    {
        return $this->hasMany(ServiceReportPhoto::class)->where('tipe', 'general');
    }
}
