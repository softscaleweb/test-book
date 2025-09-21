@extends('layouts.app')

@section('title', 'Flight details')

@section('content')
@php
$dep = isset($flight['departure']) ? \Carbon\Carbon::parse($flight['departure']) : null;
$arr = isset($flight['arrival']) ? \Carbon\Carbon::parse($flight['arrival']) : null;
@endphp

<div class="card">
  <div class="card-body">
    <h5 class="card-title">{{ $flight['airline'] ?? 'Airline' }} — {{ $flight['id'] }}</h5>
    <div class="mb-2 text-muted">
      {{ $flight['origin'] ?? '' }} → {{ $flight['destination'] ?? '' }} • {{ $dep ? $dep->format('Y-m-d') : '' }}
    </div>

    <div class="row mb-3">
      <div class="col-md-8">
        <p><strong>Departure:</strong> {{ $dep ? $dep->format('Y-m-d H:i') : '-' }}</p>
        <p><strong>Arrival:</strong> {{ $arr ? $arr->format('Y-m-d H:i') : '-' }}</p>
        <p><strong>Duration:</strong> {{ $flight['duration_mins'] ?? '-' }} minutes</p>
        <p><strong>Refundable:</strong> {{ !empty($flight['refundable']) ? 'Yes' : 'No' }}</p>
      </div>
      <div class="col-md-4 text-end">
        <div class="h4">₹{{ number_format($flight['price_in_inr'] ?? 0, 2) }}</div>
      </div>
    </div>

    <div class="d-flex gap-2">
      <form method="POST" action="{{ route('cart.flights.add') }}">
        @csrf
        <input type="hidden" name="flight_id" value="{{ $flight['id'] }}">
        <button type="submit" class="btn btn-primary">Add to cart</button>
      </form>

      <a href="{{ route('search.flights') }}" class="btn btn-outline-secondary">Back to search</a>
    </div>
  </div>
</div>
@endsection