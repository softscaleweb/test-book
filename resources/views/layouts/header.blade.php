<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">MiniBooking</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                @auth
                @role('customer')
                <li class="nav-item"><a class="nav-link" href="{{ route('search.flights') }}">Flights</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('search.hotels') }}">Hotels</a></li>
                @endrole
                @endauth
            </ul>

            <ul class="navbar-nav ms-auto align-items-center">
                {{-- Cart badge --}}
                @auth
                @role('customer')
                <li class="nav-item me-3">
                    <a class="btn btn-outline-secondary btn-sm position-relative" href="{{ route('cart.summary') }}">
                        <i class="bi bi-cart"></i>
                        <span class="ms-1">Cart</span>
                        @if(session('cart'))
                        <span class="badge bg-danger rounded-pill position-absolute"
                            style="top:-6px; right:-6px;">1</span>
                        @endif
                    </a>
                </li>
                @endrole
                @endauth
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-bs-toggle="dropdown">
                        {{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('bookings.index') }}">My Bookings</a></li>

                        @role('admin')
                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Admin Dashboard</a></li>
                        @endrole

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button class="dropdown-item" type="submit">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>