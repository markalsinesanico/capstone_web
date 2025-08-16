<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'borrower_id',
        'year',
        'department',
        'course',
        'date',
        'time_in',
        'time_out',
        'room_id',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'time_in' => 'datetime:H:i',
        'time_out' => 'datetime:H:i',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
