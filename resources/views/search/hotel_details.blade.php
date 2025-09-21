@extends('layouts.app')

@section('title', 'Hotel details')

@section('content')
<div class="card">
    <div class="card-body">
        <h5 class="card-title">{{ $hotel['name'] ?? 'Hotel' }} — {{ $hotel['room_type'] ?? '' }}</h5>
        <div class="mb-2 text-muted">{{ $hotel['city'] ?? '' }}</div>

        <div class="row mb-3">
            <div class="col-md-8">
                <p><strong>Room type:</strong> {{ $hotel['room_type'] ?? '-' }}</p>
                <p><strong>Refundable:</strong> {{ !empty($hotel['refundable']) ? 'Yes' : 'No' }}</p>
                <p><strong>Check In :</strong> {{ $check_in }}</p>
                <p><strong>Check Out:</strong> {{$check_out }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="h4">₹{{ number_format($hotel['price_per_night_in_inr'] ?? 0, 2) }} / night</div>
            </div>
        </div>

        <form method="POST" action="{{ route('cart.hotels.add') }}" class="mb-3">
            @csrf
            <input type="hidden" name="hotel_id" value="{{ $hotel['id'] }}">

            <input type="hidden" name="check_in" value="{{ $check_in }}">
            <input type="hidden" name="check_out" value="{{ $check_out }}">
            <input type="hidden" name="guests" value="1">

            <button type="submit" class="btn btn-primary">Add to cart</button>
            <a href="{{ route('search.hotels') }}" class="btn btn-outline-secondary ms-2">Back to search</a>
        </form>

        <div class="text-muted small">Adding a hotel to cart will replace any existing cart item.</div>
    </div>
</div>
@endsection