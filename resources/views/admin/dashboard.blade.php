@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12 mb-3">
        <h3>Admin Dashboard</h3>
        <p class="text-muted small">Summary of bookings and revenue.</p>
    </div>

    {{-- Summary cards --}}
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Total bookings</h6>
                <div class="display-6">{{ number_format($totalBookings) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Flights</h6>
                <div class="display-6">{{ number_format($flightCount) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Hotels</h6>
                <div class="display-6">{{ number_format($hotelCount) }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="card-title">Total revenue (INR)</h6>
                <div class="display-6">₹{{ number_format($totalRevenueInInr, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Last 5 bookings --}}
    <div class="col-12 mt-2">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Last 5 Bookings</h5>

                @if($lastFive->isEmpty())
                <p class="text-muted">No bookings yet.</p>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Type</th>
                                <th>Amount (INR)</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lastFive as $b)
                            <tr>
                                <td>{{ $b->booking_code }}</td>
                                <td>{{ ucfirst($b->type) }}</td>
                                <td>
                                    @php
                                    $pb = $b->pricingBreakdowns()->first();
                                    $amt = $pb ? ($pb->base_amount_in_inr ?? null) : ($b->total_amount ?? null);
                                    @endphp
                                    ₹{{ number_format($amt ?? 0, 2) }}
                                </td>
                                <td>{{ $b->customer->name ?? '—' }}</td>
                                <td><span
                                        class="badge bg-{{ $b->status === 'confirmed' ? 'success' : ($b->status === 'pending' ? 'warning' : 'secondary') }}">{{
                                        $b->status }}</span></td>
                                <td>{{ $b->created_at->format('Y-m-d H:i') }}</td>
                                <td><a href="{{ route('bookings.show', $b->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection