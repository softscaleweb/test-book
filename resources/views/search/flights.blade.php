@extends('layouts.app')

@section('title', 'Search Flights')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Search Flights</h5>

                <form method="GET" action="{{ route('search.flights') }}" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">Origin</label>
                        <input type="text" name="origin" value="{{ old('origin', $filters['origin'] ?? '') }}"
                            class="form-control {{ $errors->has('origin') ? 'is-invalid' : '' }}" placeholder="City">
                        @if($errors->has('origin'))
                        <div class="invalid-feedback">{{ $errors->first('origin') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Destination</label>
                        <input type="text" name="destination"
                            value="{{ old('destination', $filters['destination'] ?? '') }}"
                            class="form-control {{ $errors->has('destination') ? 'is-invalid' : '' }}"
                            placeholder="City">
                        @if($errors->has('destination'))
                        <div class="invalid-feedback">{{ $errors->first('destination') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="date" value="{{ old('date', $filters['date'] ?? '') }}"
                            class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}">
                        @if($errors->has('date'))
                        <div class="invalid-feedback">{{ $errors->first('date') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Max price (optional)</label>
                        <input type="number" name="max_price"
                            value="{{ old('max_price', $filters['max_price'] ?? '') }}" class="form-control" min="0"
                            step="0.01">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sort</label>
                        <select name="sort" class="form-select">
                            <option value="">-- none --</option>
                            <option value="price_asc" {{ ( ($filters['sort'] ?? '' )==='price_asc' ) ? 'selected' : ''
                                }}>Price: Low → High</option>
                            <option value="price_desc" {{ ( ($filters['sort'] ?? '' )==='price_desc' ) ? 'selected' : ''
                                }}>Price: High → Low</option>
                        </select>
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('search.flights') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Results --}}
        <div>
            @if(!empty($results) && count($results) > 0)
            <p class="text-muted">Showing {{ count($results) }} result(s).</p>

            @foreach($results as $r)
            @php
            $id = $r['id'];
            $dep = isset($r['departure']) ? \Carbon\Carbon::parse($r['departure']) : null;
            $arr = isset($r['arrival']) ? \Carbon\Carbon::parse($r['arrival']) : null;
            @endphp

            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">{{ $r['airline'] ?? 'Airline' }} — {{ $r['id'] }}</h6>
                            <div class="small text-muted">
                                {{ $r['origin'] ?? '' }} → {{ $r['destination'] ?? '' }}
                                • {{ $dep ? $dep->format('Y-m-d') : '' }}
                            </div>
                            <div class="small mt-1">Departure: {{ $dep ? $dep->format('H:i') : '-' }} — Arrival: {{ $arr
                                ? $arr->format('H:i') : '-' }}</div>
                            <div class="small mt-1">Duration: {{ (isset($r['duration_mins']) ? $r['duration_mins'] . '
                                mins' : '-') }}</div>
                        </div>

                        <div class="text-end">
                            <div class="h5 mb-1">₹{{ number_format($r['price_in_inr'] ?? 0, 2) }}</div>
                            <a href="{{ route('search.flight.details', $id) }}"
                                class="btn btn-outline-primary btn-sm mb-1">Details</a>

                            <form method="POST" action="{{ route('cart.flights.add') }}">
                                @csrf
                                <input type="hidden" name="flight_id" value="{{ $id }}">
                                <button type="submit" class="btn btn-primary btn-sm">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @elseif(request()->filled('origin') || request()->filled('destination') || request()->filled('date'))
            <div class="alert alert-warning">No flights found for your search.</div>
            @else
            <div class="text-muted">Enter origin, destination and date then click Search to find flights.</div>
            @endif
        </div>
    </div>

</div>
@endsection