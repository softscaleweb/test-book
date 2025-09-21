@extends('layouts.app')

@section('title', 'Checkout — Cart Summary')

@section('content')
<div class="row">
    <div class="col-md-12 row justify-content-between">
        <div class="card col-md-5 mb-3">
            <div class="card-body">
                <h5 class="card-title">Selected item</h5>

                @if(! $cart)
                <div class="text-muted">Your cart is empty. Browse <a href="{{ route('search.flights') }}">Flights</a>
                    or <a href="{{ route('search.hotels') }}">Hotels</a>.</div>
                @else
                {{-- Item summary --}}
                <div class="mb-3">
                    <h6 class="mb-1">{{ $cart['title'] ?? ucfirst($cart['type']) }}</h6>
                    <div class="small text-muted mb-2">
                        @if($cart['type'] === 'flight')
                        {{ $cart['origin'] ?? '' }} → {{ $cart['destination'] ?? '' }} • {{ $cart['date'] ?? '' }}
                        @else
                        {{ $cart['city'] ?? '' }} • {{ $cart['check_in'] ?? '' }} → {{ $cart['check_out'] ?? '' }}
                        <div>Nights: {{ $cart['nights'] ?? '-' }}</div>
                        @endif
                    </div>

                    <div>
                        <strong>
                            @if(($currency ?? 'INR') === 'INR')
                            ₹{{ number_format($displayPrice ?? ($cart['price'] ?? 0), 2) }}
                            @else
                            {{ $currency }} {{ number_format($displayPrice ?? ($cart['price'] ?? 0), 2) }}
                            @endif
                        </strong>
                    </div>
                </div>

                {{-- FX snapshot --}}
                <div class="mb-3">
                    <small class="text-muted">
                        FX snapshot:
                        @if(!empty($fxSnapshot))
                        INR => {{ $fxSnapshot['inr'] ?? 'n/a' }} |
                        USD => {{ $fxSnapshot['usd'] ?? 'n/a' }}
                        @if(!empty($fxSnapshot['fetched_at'])) — updated: {{ $fxSnapshot['fetched_at'] }} @endif
                        @else
                        FX rates not available.
                        @endif
                    </small>
                </div>

                {{-- Currency toggle --}}
                <form method="GET" action="{{ route('cart.summary') }}" class="mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <label class="mb-0">Display currency:</label>
                        <select name="currency" onchange="this.form.submit()" class="form-select w-auto">
                            <option value="INR" {{ ($currency ?? 'INR' )==='INR' ? 'selected' : '' }}>INR</option>
                            <option value="USD" {{ ($currency ?? '' )==='USD' ? 'selected' : '' }}>USD</option>
                        </select>
                    </div>
                </form>
                @endif
            </div>
        </div>

        {{-- Contact form (passenger/lead guest) --}}
        @if($cart)
        <div class="card col-md-5 ">
            <div class="card-body">
                <h5 class="card-title">Contact details</h5>

                <form method="POST" action="{{ route('cart.confirm') }}">
                    @csrf

                    {{-- keep currency for confirm --}}
                    <input type="hidden" name="currency" value="{{ $currency ?? 'INR' }}">

                    <div class="mb-3">
                        <label class="form-label">Full name</label>
                        <input type="text" name="name" value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Passenger or contact full name">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            class="form-control @error('email') is-invalid @enderror" placeholder="you@example.com">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="number" name="phone" value="{{ old('phone') }}"
                            class="form-control @error('phone') is-invalid @enderror" placeholder="9999989999">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success">Confirm Booking</button>
                        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection