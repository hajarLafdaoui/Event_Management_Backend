Profile Mgmt → 2. Portfolio → 3. Services → 4. Pricing → 5. Admin Approval → (Then: Bookings, Chat, Payments, Reviews)
Create the migrations for the vendor management system (vendor profile, portfolio, services, pricing packages, availability, and admin approvals).
# 1. Vendor Categories (Prerequisite for vendors)
php artisan make:migration create_vendor_categories_table

# 2. Vendor Profile Management
php artisan make:migration create_vendors_table

# 3. Vendor Portfolio
php artisan make:migration create_vendor_portfolios_table

# 4. Vendor Services
php artisan make:migration create_vendor_services_table

# 5. Pricing Packages
php artisan make:migration create_vendor_pricing_packages_table

# 6. Vendor Availability
php artisan make:migration create_vendor_availabilities_table

# 7. Admin Approvals
php artisan make:migration create_vendor_approvals_table
1
Schema::create('vendor_categories', function (Blueprint $table) {
    $table->id();
    $table->string('name'); // "Photographer", "Caterer", "Venue"
            $table->text('description')->nullable();

    $table->timestamps();
});     

2
Schema::create('vendors', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Links to Users table
    $table->foreignId('vendor_category_id')->constrained(); // Primary service type
    $table->string('business_name');
    $table->text('description');
    $table->string('contact_email');
    $table->string('contact_phone');
    $table->string('address');
    $table->string('website')->nullable();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->text('rejection_reason')->nullable();
    $table->timestamps();
});

3
Schema::create('vendor_portfolios', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->enum('type', ['image', 'video']);
    $table->string('url'); // Path to file (S3/cloud)
    $table->string('caption')->nullable();
    $table->timestamps();
});


4
Schema::create('vendor_services', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->string('name'); // "Wedding Photography Package"
    $table->text('description')->nullable();
    $table->timestamps();
});


5
Schema::create('vendor_pricing_packages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_service_id')->constrained()->cascadeOnDelete();
    $table->string('name'); // "Basic", "Premium"
    $table->decimal('price', 10, 2);
    $table->text('features')->nullable(); // JSON or comma-separated
    $table->timestamps();
});

6
Schema::create('vendor_availabilities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->date('date');
    $table->time('start_time');
    $table->time('end_time');
    $table->boolean('is_available')->default(true);
    $table->timestamps();
});

7
Schema::create('vendor_approvals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
    $table->foreignId('admin_id')->constrained('users'); // Admin user
    $table->enum('action', ['approved', 'rejected']);
    $table->text('notes')->nullable();
    $table->timestamps();
});

