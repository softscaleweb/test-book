<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
    public function show()
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Customer -> flight search (customer-protected)
            if ($user->hasRole('customer')) {
                return redirect()->route('search.flights');
            }

            // Admin -> admin dashboard (admin-protected)
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('home');
        }

        return view('login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');

        $remember = (bool) $request->input('remember', false);

        if (!Auth::attempt($credentials, $remember)) {
            return back()
                ->withErrors(['email' => 'Invalid credentials provided.'])
                ->withInput($request->only('email', 'remember'));
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect admins to admin dashboard, customers to search.
        if ($user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        }

        // Default customer redirect (or intended)
        return redirect()->intended(route('search.flights'));
    }

    /**
     * Log the user out (invalidate the session).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
