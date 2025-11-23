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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_link')->nullable();
            $table->text('ticket_description')->nullable();
            $table->string('dealer_code')->nullable();
            $table->string('project_name')->nullable();
            $table->string('website_link')->nullable();
            $table->string('created_by')->nullable();
            $table->string('reference_image_link')->nullable();
            $table->string('current_image_link')->nullable();
            $table->string('status')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();
        });
    }   

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
