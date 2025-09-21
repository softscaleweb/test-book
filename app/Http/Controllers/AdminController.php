<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\PricingBreakdown;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // total bookings
        $totalBookings = Booking::count();

        // bookings by type (flight/hotel)
        $bookingsByType = Booking::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type') // [ 'flight' => 10, 'hotel' => 5 ]
            ->toArray();

        $flightCount = $bookingsByType['flight'] ?? 0;
        $hotelCount = $bookingsByType['hotel'] ?? 0;

        // total revenue in INR:
        // we store base_amount_in_inr in pricing_breakdowns; sum them for confirmed bookings.
        // join pricing_breakdowns -> bookings to consider status if needed.
        $totalRevenueInInr = PricingBreakdown::selectRaw('COALESCE(SUM(base_amount_in_inr),0) as total')
            ->join('bookings', 'pricing_breakdowns.booking_id', '=', 'bookings.id')
            ->where('bookings.status', 'confirmed')
            ->value('total') ?? 0.0;

        // last 5 bookings (most recent)
        $lastFive = Booking::with(['customer'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'totalBookings' => (int) $totalBookings,
            'flightCount' => (int) $flightCount,
            'hotelCount' => (int) $hotelCount,
            'totalRevenueInInr' => (float) $totalRevenueInInr,
            'lastFive' => $lastFive,
        ]);
    }
}
