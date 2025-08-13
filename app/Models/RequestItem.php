<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestItem extends Model
{
    protected $table = 'requests';

    protected $fillable = [
        'name',
        'borrower_id',
        'date',
        'time_in',
        'time_out',
        'item_id',
        'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}