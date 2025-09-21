@extends('layouts.app')

@section('title', 'Booking details')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">Booking {{ $booking->booking_code }}</h5>
                <p class="mb-1"><strong>Type:</strong> {{ ucfirst($booking->type) }}</p>
                <p class="mb-1"><strong>Status:</strong> {{ $booking->status }}</p>
                <p class="mb-1"><strong>Customer:</strong> {{ $booking->customer->name ?? '-' }} ({{
                    $booking->customer->email ?? '-' }})</p>

                {{-- item from metadata if available --}}
                @if($booking->metadata)
                <hr>
                <h6>Item snapshot</h6>
                <pre class="small">{{ json_encode($booking->metadata, JSON_PRETTY_PRINT) }}</pre>
                @endif

                <hr>
                <h6>Pricing</h6>
                @foreach($booking->pricingBreakdowns as $pb)
                <p><strong>Base amount (INR):</strong> ₹{{ number_format($pb->base_amount_in_inr,2) }}</p>
                <p><strong>Total ({{ $pb->currency }}):</strong> {{ $pb->currency }} {{
                    number_format($pb->total_in_currency,2) }}</p>
                <p><strong>FX snapshot:</strong></p>
                <pre class="small">{{ json_encode($pb->fx_rate_at_booking, JSON_PRETTY_PRINT) }}</pre>
                @endforeach

                <hr>
                <h6>Passengers / Guests</h6>
                @foreach($booking->passengersOrGuests as $p)
                <p class="mb-1">{{ $p->name }} — {{ $p->email ?? '-' }} — {{ $p->phone ?? '-' }}</p>
                @endforeach
            </div>
        </div>

        <a href="{{ route('bookings.index') }}" class="btn btn-outline-secondary">Back to List</a>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-body small text-muted">
                Booking created at: {{ $booking->created_at->toDayDateTimeString() }}<br>
                Booking code: <strong>{{ $booking->booking_code }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection