<?php

namespace App\Http\Controllers;

use App\Jobs\SendBookingEmailJob;
use App\Models\Booking;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Show checkout summary and contact form.
     */
    public function index(Request $request)
    {
        $cart = session('cart');

        if (!$cart) {
            return view('cart.summary', [
                'cart' => null,
                'currency' => 'INR',
                'displayPrice' => null,
                'fxSnapshot' => null,
            ]);
        }

        $currency = strtoupper($request->input('currency', 'INR'));
        if (!in_array($currency, ['INR', 'USD'])) {
            $currency = 'INR';
        }

        // Use Currency model to fetch rates
        $fxInr = Currency::where('code', 'INR')->first();
        $fxUsd = Currency::where('code', 'USD')->first();

        $priceInInr = (float) ($cart['price'] ?? 0);

        // convert for display
        $displayPrice = convertCurrency($priceInInr, 'INR', $currency);

        // prepare fx snapshot for display
        $fxSnapshot = [
            'inr' => $fxInr ? (float) $fxInr->value : 1.0,
            'usd' => $fxUsd ? (float) $fxUsd->value : null,
            'fetched_at' => $fxUsd ? $fxUsd->updated_at->toDateTimeString() : null,
        ];

        return view('cart.summary', [
            'cart' => $cart,
            'currency' => $currency,
            'displayPrice' => $displayPrice,
            'fxSnapshot' => $fxSnapshot,
        ]);
    }

    public function confirm(Request $request)
    {
        $cart = session('cart');
        if (!$cart) {
            return redirect()->route('cart.summary')->with('error', 'Cart is empty.');
        }

        $v = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'currency' => ['required', 'in:INR,USD'],
        ]);

        $v->validate();

        // get fresh FX snapshot from DB
        $fxInr = Currency::where('code', 'INR')->first();
        $fxUsd = Currency::where('code', 'USD')->first();

        $fxRateSnapshot = [
            'inr' => $fxInr ? (float) $fxInr->value : 1.0,
            'usd' => $fxUsd ? (float) $fxUsd->value : null,
            'saved_at' => now()->toDateTimeString(),
        ];

        $currency = $request->input('currency', 'INR');

        $baseAmountInInr = (float) ($cart['price'] ?? 0); 
        $totalInCurrency = ($currency === 'INR') ? $baseAmountInInr : convertCurrency($baseAmountInInr, 'INR', $currency);

        // booking code
        $bookingCode = strtoupper('BK' . Str::random(6));

        DB::beginTransaction();
        try {
            // create booking
            $booking = Booking::create([
                'booking_code' => $bookingCode,
                'type' => $cart['type'] ?? 'unknown',
                'item_id' => $cart['id'] ?? null,
                'customer_id' => Auth::id(),
                'currency' => $currency,
                'total_amount' => round($totalInCurrency, 2),
                'status' => 'confirmed',
            ]);

            // pricing breakdown
            $booking->pricingBreakdowns()->create([
                'base_amount_in_inr' => round($baseAmountInInr, 2),
                'currency' => $currency,
                'fx_rate_at_booking' => $fxRateSnapshot,
                'total_in_currency' => round($totalInCurrency, 2),
            ]);

            // passenger/guest — store contact as lead passenger/guest
            $booking->passengersOrGuests()->create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }

        // dispatch fake email job
        SendBookingEmailJob::dispatch([
            'booking_id' => $booking->id,
            'booking_code' => $bookingCode,
            'contact_name' => $request->input('name'),
            'contact_email' => $request->input('email'),
            'item' => $cart,
        ]);

        // clear cart
        session()->forget('cart');

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking confirmed! Booking code: ' . $bookingCode);
    }
}
