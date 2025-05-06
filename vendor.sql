1. vendor_categories →
2. vendors →

3. vendor_portfolios → 

4. vendor_services → 
5. vendor_pricing_packages → 
6. vendor_availabilities → 
7. vendor_approvals →
(Then: Bookings, Chat, Payments, Reviews)

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
    $table->string('country')->default('Morocco'); // Set a default if needed
    $table->string('city');
    $table->string('street_address');      // e.g., "123 Main St"
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

