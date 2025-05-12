
-- =============================================
-- USER MANAGEMENT
-- =============================================

CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    user_type ENUM('client', 'vendor', 'admin') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL
    last_login TIMESTAMP NULL,
    reset_token VARCHAR(255),
    provider ENUM('local', 'google', 'facebook') DEFAULT 'local',
    provider_id VARCHAR(255) UNIQUE;
);

-- =============================================
-- EVENT MANAGEMENT
-- =============================================

CREATE TABLE event_types (
    event_type_id INT PRIMARY KEY AUTO_INCREMENT,
    type_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_by_admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_admin_id) REFERENCES users(user_id)
);

CREATE TABLE event_templates (
    template_id INT PRIMARY KEY AUTO_INCREMENT,
    event_type_id INT NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    template_description TEXT,
    default_budget DECIMAL(12,2),
    created_by_admin_id INT,
    is_system_template BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_type_id) REFERENCES event_types(event_type_id),
    FOREIGN KEY (created_by_admin_id) REFERENCES users(user_id)
);

CREATE TABLE events (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_type_id INT NOT NULL,
    template_id INT,
    event_name VARCHAR(255) NOT NULL,
    event_description TEXT,
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    venue_name VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    budget DECIMAL(12,2),
    current_spend DECIMAL(12,2) DEFAULT 0.00,
    status ENUM('draft', 'planned', 'in_progress', 'completed', 'cancelled') DEFAULT 'draft',
    theme VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (event_type_id) REFERENCES event_types(event_type_id),
    FOREIGN KEY (template_id) REFERENCES event_templates(template_id)
);

-- =============================================
-- TASK MANAGEMENT
-- =============================================

CREATE TABLE task_templates (
    task_template_id INT PRIMARY KEY AUTO_INCREMENT,
    event_type_id INT NOT NULL,
    template_name VARCHAR(255) NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_description TEXT,
    default_days_before_event INT,
    default_priority ENUM('low', 'medium', 'high'),
    default_duration_hours INT,
    is_system_template BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_type_id) REFERENCES event_types(event_type_id)
);

CREATE TABLE event_tasks (
    task_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    task_name VARCHAR(255) NOT NULL,
    task_description TEXT,
    assigned_to ENUM('client', 'vendor', 'none') DEFAULT 'none',
    assigned_vendor_id INT,
    due_date DATE,
    due_datetime DATETIME,
    completed_at DATETIME,
    status ENUM('not_started', 'in_progress', 'completed', 'cancelled') DEFAULT 'not_started',
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    progress_percentage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_vendor_id) REFERENCES vendors(vendor_id)
);

-- =============================================
-- VENDOR MANAGEMENT
-- =============================================
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

:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
(Then: Bookings, Chat, Payments, Reviews) ismail

CREATE TABLE booking_requests (
    booking_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    vendor_id INT NOT NULL,
    service_id INT NOT NULL,
    package_id INT,
    requested_date DATE NOT NULL,
    start_time TIME,
    end_time TIME,
    special_requests TEXT,
    estimated_price DECIMAL(10,2),
    status ENUM('pending', 'accepted', 'rejected', 'cancelled') DEFAULT 'pending',
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id),
    FOREIGN KEY (service_id) REFERENCES vendor_services(service_id),
    FOREIGN KEY (package_id) REFERENCES service_packages(package_id)
);
CREATE TABLE messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    related_booking_id INT,
    message_text TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id),
    FOREIGN KEY (related_booking_id) REFERENCES booking_requests(booking_id)
);

CREATE TABLE vendor_payments (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('stripe', 'paypal') NOT NULL,
    transaction_id VARCHAR(255),
    payment_status ENUM('pending', 'processing', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    client_id INT NOT NULL,
    vendor_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES booking_requests(booking_id),
    FOREIGN KEY (client_id) REFERENCES users(user_id),
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id)
);

CREATE TABLE vendor_reviews (
    review_id INT PRIMARY KEY AUTO_INCREMENT,
    vendor_id INT NOT NULL,
    client_id INT NOT NULL,
    booking_id INT,
    rating TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    review_text TEXT,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_approved BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(vendor_id),
    FOREIGN KEY (client_id) REFERENCES users(user_id),
    FOREIGN KEY (booking_id) REFERENCES booking_requests(booking_id)
);
-- =============================================
-- GUEST MANAGEMENT
-- =============================================
--hajar
CREATE TABLE guest_lists (
    guest_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    country VARCHAR(100),
    postal_code VARCHAR(20),
    is_primary_guest BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE

);
--hajar
CREATE TABLE invitations (
    invitation_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    guest_id INT NOT NULL,
    sent_via ENUM('email', 'sms', 'both'),
    sent_at TIMESTAMP NULL,
    rsvp_status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    responded_at TIMESTAMP NULL,
    response_notes TEXT,
    -- token VARCHAR(255) UNIQUE,
    is_reminder_sent BOOLEAN DEFAULT FALSE,
    reminder_sent_at TIMESTAMP NULL,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (guest_id) REFERENCES guest_lists(guest_id) ON DELETE CASCADE
);



--ismail
CREATE TABLE email_templates (
    template_id INT PRIMARY KEY AUTO_INCREMENT,
    template_name VARCHAR(255) NOT NULL, green invitation   sea incitation
    template_subject VARCHAR(255) NOT NULL,
    template_body TEXT NOT NULL,
    is_system_template BOOLEAN DEFAULT TRUE,
    created_by_admin_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by_admin_id) REFERENCES users(user_id)
);

--hajar
CREATE TABLE sent_emails (
    email_id INT PRIMARY KEY AUTO_INCREMENT,
    template_id INT,
    event_id INT,
    sender_id INT NOT NULL,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('sent', 'delivered', 'failed') DEFAULT 'sent',
    FOREIGN KEY (template_id) REFERENCES email_templates(template_id),
    FOREIGN KEY (event_id) REFERENCES events(event_id),
    FOREIGN KEY (sender_id) REFERENCES users(user_id)
);

-- =============================================
-- REVIEWS & MEDIA
-- =============================================


-- hajar  done with event -> sending email to all the guests (form) -> 
CREATE TABLE event_feedback (
    feedback_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    guest_id INT NOT NULL,
    rating TINYINT CHECK (rating BETWEEN 1 AND 5),
    feedback_text TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (guest_id) REFERENCES guest_lists(guest_id) ON DELETE CASCADE
);


-- ismail
CREATE TABLE event_gallery (
    gallery_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    uploader_id INT NOT NULL,
    media_url VARCHAR(255) NOT NULL,
    media_type ENUM('image', 'video') NOT NULL,
    caption VARCHAR(255),
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (uploader_id) REFERENCES users(user_id)
);

--ismail
CREATE TABLE event_documents (
    document_id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    uploader_id INT NOT NULL,
    file_url VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50),
    file_size INT,
    description TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id) ON DELETE CASCADE,
    FOREIGN KEY (uploader_id) REFERENCES users(user_id)
);

