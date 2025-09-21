@extends('layouts.app')

@section('title', 'Search Hotels')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Search Hotels</h5>

                <form method="GET" action="{{ route('search.hotels') }}" class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label">City</label>
                        <input type="text" name="city" value="{{ old('city', $filters['city'] ?? '') }}"
                            class="form-control {{ $errors->has('city') ? 'is-invalid' : '' }}">
                        @if($errors->has('city'))
                        <div class="invalid-feedback">{{ $errors->first('city') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Check-in</label>
                        <input type="date" name="check_in" value="{{ old('check_in', $filters['check_in'] ?? '') }}"
                            class="form-control {{ $errors->has('check_in') ? 'is-invalid' : '' }}">
                        @if($errors->has('check_in'))
                        <div class="invalid-feedback">{{ $errors->first('check_in') }}</div>
                        @endif
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Check-out</label>
                        <input type="date" name="check_out" value="{{ old('check_out', $filters['check_out'] ?? '') }}"
                            class="form-control {{ $errors->has('check_out') ? 'is-invalid' : '' }}">
                        @if($errors->has('check_out'))
                        <div class="invalid-feedback">{{ $errors->first('check_out') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Max price (optional)</label>
                        <input type="number" name="max_price"
                            value="{{ old('max_price', $filters['max_price'] ?? '') }}"
                            class="form-control {{ $errors->has('max_price') ? 'is-invalid' : '' }}" min="0"
                            step="0.01">
                        @if($errors->has('max_price'))
                        <div class="invalid-feedback">{{ $errors->first('max_price') }}</div>
                        @endif
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Sort</label>
                        <select name="sort" class="form-select {{ $errors->has('sort') ? 'is-invalid' : '' }}">
                            <option value="">-- none --</option>
                            <option value="price_asc" {{ ( ($filters['sort'] ?? '' )==='price_asc' ) ? 'selected' : ''
                                }}>
                                Price: Low → High
                            </option>
                            <option value="price_desc" {{ ( ($filters['sort'] ?? '' )==='price_desc' ) ? 'selected' : ''
                                }}>
                                Price: High → Low
                            </option>
                        </select>
                        @if($errors->has('sort'))
                        <div class="invalid-feedback">{{ $errors->first('sort') }}</div>
                        @endif
                    </div>

                    <div class="col-md-6 d-flex align-items-end">
                        <div>
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="{{ route('search.hotels') }}" class="btn btn-outline-secondary">Reset</a>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div>
            @if(!empty($results) && count($results) > 0)
            <p class="text-muted">Showing {{ count($results) }} result(s).</p>

            @foreach($results as $h)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-1">{{ $h['name'] ?? 'Hotel' }} — {{ $h['room_type'] ?? '' }}</h6>
                            <div class="small text-muted">{{ $h['city'] ?? '' }}</div>
                        </div>

                        <div class="text-end">
                            <div class="h5 mb-1">₹{{ number_format($h['price_per_night_in_inr'] ?? 0, 2) }} / night
                            </div>
                            <form method="POST" action="{{ route('search.hotel.details') }}" class="mt-1">
                                @csrf
                                <input type="hidden" name="hotel_id" value="{{ $h['id'] }}">

                                <input type="hidden" name="check_in" value="{{ request()->input('check_in') }}">
                                <input type="hidden" name="check_out" value="{{ request()->input('check_out') }}">
                                <input type="hidden" name="guests" value="1">

                                <button type="submit" class="btn btn-outline-primary btn-sm mb-1">Details</button>
                            </form>

                            <form method="POST" action="{{ route('cart.hotels.add') }}" class="mt-1">
                                @csrf
                                <input type="hidden" name="hotel_id" value="{{ $h['id'] }}">

                                <input type="hidden" name="check_in" value="{{ request()->input('check_in') }}">
                                <input type="hidden" name="check_out" value="{{ request()->input('check_out') }}">
                                <input type="hidden" name="guests" value="1">

                                <button type="submit" class="btn btn-primary btn-sm">Add to cart</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @elseif(request()->filled('city') || request()->filled('check_in') || request()->filled('check_out'))
            <div class="alert alert-warning">No hotels found for your search.</div>
            @else
            <div class="text-muted">Enter city, check-in and check-out then click Search to find hotels.</div>
            @endif
        </div>
    </div>

</div>
@endsection