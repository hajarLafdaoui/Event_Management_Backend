<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Vendor\Vendor;
use App\Models\VendorPayment;
use App\Models\BookingRequest; // Add this
use Illuminate\Database\Seeder;

class VendorPaymentSeeder extends Seeder
{
    public function run()
    {
        // First, seed some booking requests
        $bookingSeeder = new BookingRequestSeeder();
        $bookingSeeder->run();
        
        $bookings = BookingRequest::all();
        $vendors = Vendor::all();
        $clients = User::where('role', 'client')->get();

        $payments = [
            [
                'amount' => 1500.00,
                'payment_method' => 'stripe',
                'transaction_id' => 'ch_'.strtoupper(bin2hex(random_bytes(8))),
                'payment_status' => 'completed',
            ],
            [
                'amount' => 2500.50,
                'payment_method' => 'paypal',
                'transaction_id' => 'PAYID-'.strtoupper(bin2hex(random_bytes(8))),
                'payment_status' => 'processing',
            ],
            [
                'amount' => 3200.75,
                'payment_method' => 'stripe',
                'transaction_id' => 'ch_'.strtoupper(bin2hex(random_bytes(8))),
                'payment_status' => 'failed',
            ],
            [
                'amount' => 1800.25,
                'payment_method' => 'paypal',
                'transaction_id' => 'PAYID-'.strtoupper(bin2hex(random_bytes(8))),
                'payment_status' => 'refunded',
            ],
            [
                'amount' => 4200.00,
                'payment_method' => 'stripe',
                'transaction_id' => 'ch_'.strtoupper(bin2hex(random_bytes(8))),
                'payment_status' => 'completed',
            ],
        ];

        foreach ($payments as $index => $payment) {
            VendorPayment::create([
                'booking_id' => $bookings[$index]->booking_id, // Use existing booking
                'client_id' => $clients->random()->id,
                'vendor_id' => $vendors->random()->id,
                'amount' => $payment['amount'],
                'payment_method' => $payment['payment_method'],
                'transaction_id' => $payment['transaction_id'],
                'payment_status' => $payment['payment_status'],
                'payment_date' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}