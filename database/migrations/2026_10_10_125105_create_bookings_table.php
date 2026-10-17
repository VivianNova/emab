<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
      Schema::create('bookings', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone');
    $table->string('email')->nullable();
    $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
    $table->string('address');
    $table->date('preferred_date');
    $table->string('preferred_time');          // morning, afternoon or evening
    $table->text('notes')->nullable();
    $table->string('status')->default('new');
    $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
    $table->text('admin_notes')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
