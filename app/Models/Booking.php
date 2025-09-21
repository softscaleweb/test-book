<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $fillable = [
        'booking_code',
        'type',
        'item_id',
        'customer_id',
        'currency',
        'total_amount',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // The customer who created the booking.
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Pricing breakdown(s) for this booking.
    public function pricingBreakdowns()
    {
        return $this->hasMany(PricingBreakdown::class, 'booking_id');
    }

    //  Passengers or guests attached to this booking.
    public function passengersOrGuests()
    {
        return $this->hasMany(PassengerOrGuest::class, 'booking_id');
    }
}
