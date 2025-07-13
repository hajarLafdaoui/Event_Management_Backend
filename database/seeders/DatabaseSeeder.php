<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\EventSeeder;
use Database\Seeders\VendorSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\EventTaskSeeder;
use Database\Seeders\EventTypeSeeder;
use Database\Seeders\GuestListSeeder;
use Database\Seeders\SentEmailSeeder;
use Database\Seeders\InvitationSeeder;
use Database\Seeders\TaskTemplateSeeder;
use Database\Seeders\VendorReviewSeeder;
use Database\Seeders\EventTemplateSeeder;
use Database\Seeders\VendorPaymentSeeder;
use Database\Seeders\BookingRequestSeeder;
use Database\Seeders\VendorApprovalSeeder;
use Database\Seeders\VendorPortfolioSeeder;
use Database\Seeders\VendorAvailabilitySeeder;
use Database\Seeders\VendorPricingPackageSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
    public function run()
    {
        $this->call([
            UserSeeder::class,
            EventTypeSeeder::class,
            EventTemplateSeeder::class,
            EventSeeder::class,
            TaskTemplateSeeder::class,
            EventTaskSeeder::class,
            VendorCategorySeeder::class,
            VendorSeeder::class,
            VendorServiceSeeder::class,
            VendorPricingPackageSeeder::class,
            VendorPortfolioSeeder::class,
            VendorAvailabilitySeeder::class,
            VendorApprovalSeeder::class,
            BookingRequestSeeder::class,
            VendorReviewSeeder::class,
            VendorPaymentSeeder::class,
            MessageSeeder::class,
            EmailTemplateSeeder::class,
            GuestListSeeder::class,
            InvitationSeeder::class,
            SentEmailSeeder::class,

        ]);

    }


}

