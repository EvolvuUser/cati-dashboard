<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MobileCall extends Model
{
    protected $fillable = [
        'db_no',
        'campaign_id',
        'call_date',
        'start_epoch',
        'end_epoch',
        'length_in_sec',
        'user',
        'status_name',
    ];

    protected $casts = [
        'call_date' => 'date',
        'start_epoch' => 'integer',
        'end_epoch' => 'integer',
        'length_in_sec' => 'integer',
    ];
}
