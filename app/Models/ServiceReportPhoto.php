<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceReportPhoto extends Model
{
    protected $fillable = [
        'service_report_id',
        'service_report_item_id',
        'photo_path',
        'tipe',
    ];

    public function serviceReport()
    {
        return $this->belongsTo(ServiceReport::class);
    }

    public function serviceReportItem()
    {
        return $this->belongsTo(ServiceReportItem::class);
    }
}
