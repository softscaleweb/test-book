<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PassengerOrGuest extends Model
{
    use HasFactory;

    protected $table = 'passengers_or_guests';

    protected $fillable = [
        'booking_id',
        'name',
        'email',
        'phone',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Booking that this passenger/guest belongs to.

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
