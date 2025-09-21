<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HotelService;
use Carbon\Carbon;


class HotelController extends Controller
{
    protected HotelService $service;

    public function __construct(HotelService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $results = [];
        $filters = [];

        if ($request->hasAny(['city', 'check_in', 'check_out', 'sort', 'max_price'])) {
            $request->validate([
                'city' => ['required', 'string', 'max:100'],
                'check_in' => ['required', 'date', 'date_format:Y-m-d'],
                'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
                'sort' => ['nullable', 'in:price_asc,price_desc'],
                'max_price' => ['nullable', 'numeric'],
            ]);

            $filters = [
                'city' => trim($request->input('city')),
                'check_in' => trim($request->input('check_in')),
                'check_out' => trim($request->input('check_out')),
                'sort' => $request->input('sort'),
                'max_price' => $request->input('max_price'),
            ];

            $results = $this->service->search($filters);
        }

        return view('search.hotels', [
            'filters' => $filters,
            'results' => $results,
        ]);
    }

    public function show(Request $request)
    {
        $id = $request->hotel_id;
        $hotel = $this->service->findById($id);

        if (!$hotel) {
            abort(404, 'Hotel not found');
        }

        $check_in = $request->check_in;
        $check_out = $request->check_out;
        return view('search.hotel_details', [
            'hotel' => $hotel,
            'check_in' => $check_in,
            'check_out' => $check_out,
        ]);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'hotel_id' => ['required', 'string'],
            'check_in' => ['required', 'date', 'date_format:Y-m-d'],
            'check_out' => ['required', 'date', 'date_format:Y-m-d', 'after:check_in'],
            'guests' => ['nullable', 'integer', 'min:1'],
        ]);

        $hotelId = $request->input('hotel_id');
        $hotel = $this->service->findById($hotelId);

        if (!$hotel) {
            return back()->with('error', 'Selected hotel not found.');
        }

        $checkIn = Carbon::parse($request->input('check_in'));
        $checkOut = Carbon::parse($request->input('check_out'));
        $nights = $checkIn->diffInDays($checkOut);

        // compute total price (nights * price_per_night_in_inr)
        $pricePerNight = (float) ($hotel['price_per_night_in_inr'] ?? 0);
        $total = $pricePerNight * max(1, $nights);

        $cartItem = [
            'type' => 'hotel',
            'id' => $hotel['id'],
            'title' => ($hotel['name'] ?? '') . ' — ' . ($hotel['room_type'] ?? ''),
            'city' => $hotel['city'] ?? '',
            'room_type' => $hotel['room_type'] ?? '',
            'check_in' => $checkIn->format('Y-m-d'),
            'check_out' => $checkOut->format('Y-m-d'),
            'nights' => $nights,
            'price_per_night' => $pricePerNight,
            'price' => $total,
            'currency' => 'INR',
            'meta' => $hotel,
        ];

        session(['cart' => $cartItem]);

        return redirect()->route('cart.summary')->with('success', 'Hotel added to cart.');
    }
}
