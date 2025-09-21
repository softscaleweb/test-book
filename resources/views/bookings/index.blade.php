@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')


<div class="card">
    <div class="card-body">
        <h5 class="card-title">Bookings</h5>

        @if($bookings->count())
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Code</th>
                        <th>Type</th>
                        <th>Total Amount</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Booked By</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $b)
                    <tr>
                        <td>{{ $b->booking_code }}</td>
                        <td>{{ ucfirst($b->type) }}</td>
                        <td>{{ number_format($b->total_amount, 2) }}</td>
                        <td>{{ $b->currency }}</td>
                        <td>
                            <span class="badge bg-info">{{ $b->status }}</span>
                        </td>
                        <td>{{ $b->customer->name ?? 'N/A' }}</td>
                        <td>{{ $b->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href={{route("bookings.show" , $b->id )}} class="btn btn-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $bookings->links('pagination::bootstrap-5') }}
            </div>
        </div>

        @else
        <p class="text-muted">No bookings found.</p>
        @endif
    </div>
</div>
@endsection