<?php

namespace App\Http\Controllers;

use App\Http\Requests\FlightSearchRequest;
use App\Services\FlightService;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    protected FlightService $service;

    public function __construct(FlightService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $results = [];
        $filters = [];

        if ($request->hasAny(['origin', 'destination', 'date', 'sort', 'max_price'])) {
            $request->validate([
                'origin' => ['required', 'string', 'max:100'],
                'destination' => ['required', 'string', 'max:100'],
                'date' => ['required', 'date', 'date_format:Y-m-d'],
                'sort' => ['nullable', 'in:price_asc,price_desc'],
                'max_price' => ['nullable', 'numeric'],
            ]);

            // Use validated values as filters
            $filters = $request->only(['origin', 'destination', 'date', 'sort', 'max_price']);

            $results = $this->service->search($filters);
        }

        return view('search.flights', [
            'filters' => $filters,
            'results' => $results,
        ]);
    }

    public function show($id)
    {
        $flight = $this->service->findById($id);

        if (!$flight) {
            abort(404, 'Flight not found');
        }

        $idToUse = $flight['id'] ?? md5(($flight['airline'] ?? '') . '|' . ($flight['flight_number'] ?? '') . '|' . ($flight['date'] ?? ''));

        return view('search.flight_details', [
            'flight' => $flight,
            'flightId' => $idToUse,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'flight_id' => ['required', 'string'],
        ]);

        $flightId = $request->input('flight_id');

        $flight = $this->service->findById($flightId);

        if (!$flight) {
            return back()->with('error', 'Selected flight not found.');
        }

        $cartItem = [
            'type' => 'flight',
            'id' => $flight['id'] ?? md5(($flight['airline'] ?? '') . '|' . ($flight['flight_number'] ?? '') . '|' . ($flight['date'] ?? '')),
            'title' => ($flight['airline'] ?? '') . ' ' . ($flight['flight_number'] ?? ''),
            'origin' => $flight['origin'] ?? '',
            'destination' => $flight['destination'] ?? '',
            'date' => $flight['date'] ?? '',
            'price' => (float) ($flight['price_in_inr'] ?? 0),
            'currency' => $flight['currency'] ?? 'INR',
            'meta' => $flight,
        ];

        // Replace previous cart item
        session(['cart' => $cartItem]);

        return redirect()->route('cart.summary')->with('success', 'Flight added to cart.');
    }
}
