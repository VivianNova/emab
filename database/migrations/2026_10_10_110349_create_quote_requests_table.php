<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Only use this if you have NOT already created quote_requests in step 3 of the plan.
    public function up(): void
    {
     Schema::create('quote_requests', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone');
    $table->string('email')->nullable();
    $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
    $table->string('location');
    $table->text('description');
    $table->string('photo')->nullable();
    $table->string('status')->default('new');
    $table->text('admin_notes')->nullable();
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};