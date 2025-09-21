<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $query = Booking::with(['customer', 'pricingBreakdowns', 'passengersOrGuests'])
            ->orderBy('created_at', 'desc');

        if (!auth()->user()->hasRole('admin')) {
            $query->where('customer_id', auth()->id());
        }

        $bookings = $query->paginate(5);

        return view('bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::with('pricingBreakdowns', 'passengersOrGuests', 'customer')->findOrFail($id);

        if (auth()->id() !== $booking->customer_id && !auth()->user()->hasRole('admin')) {
            abort(403);
        }

        return view('bookings.show', compact('booking'));
    }
}
