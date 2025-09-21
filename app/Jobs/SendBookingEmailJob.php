<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBookingEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $bookingData;

    /**
     * Create a new job instance.
     *
     * @param array $bookingData Minimal array with booking info
     */
    public function __construct(array $bookingData)
    {
        $this->bookingData = $bookingData;
    }

    public function handle(): void
    {
        $to = $this->bookingData['contact_email'] ?? ($this->bookingData['email'] ?? null);
        $bookingCode = $this->bookingData['booking_code'] ?? ($this->bookingData['booking_code'] ?? null);

        $message = [
            'action' => 'send_fake_booking_email',
            'to' => $to,
            'booking_code' => $bookingCode,
            'payload' => $this->bookingData,
            'dispatched_at' => now()->toDateTimeString(),
        ];

        // Log at info level. Find this in storage/logs/laravel.log
        Log::info('SendBookingEmailJob: fake email', $message);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendBookingEmailJob failed', [
            'error' => $exception->getMessage(),
            'booking' => $this->bookingData,
        ]);
    }
}
