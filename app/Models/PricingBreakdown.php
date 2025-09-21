<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingBreakdown extends Model
{
    use HasFactory;

    protected $table = 'pricing_breakdowns';

    protected $fillable = [
        'booking_id',
        'base_amount_in_inr',
        'currency',
        'fx_rate_at_booking',
        'total_in_currency',
    ];

    protected $casts = [
        'fx_rate_at_booking' => 'array',
        'base_amount_in_inr' => 'decimal:2',
        'total_in_currency' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Booking that this breakdown belongs to.

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }
}
