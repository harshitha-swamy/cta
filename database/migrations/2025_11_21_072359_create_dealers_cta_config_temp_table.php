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
        Schema::create('dealers_cta_config_temp', function (Blueprint $table) {

            $table->integer('dealer_code')->primary(); // Primary key

            $table->string('button_class')->nullable();
            $table->string('button_class_vdp')->nullable();
            $table->string('button_class_cpov')->nullable();

            $table->string('button_text')->nullable();
            $table->string('button_text_vdp')->nullable();
            $table->string('button_text_cpov')->nullable();

            $table->string('button_image_url')->nullable();
            $table->string('button_image_url_vdp')->nullable();
            $table->string('button_image_url_cpov')->nullable();

            $table->string('image_style')->default('');
            $table->string('image_style_vdp')->nullable();
            $table->string('image_style_cpov')->default('');

            $table->enum('dealer_type', ['DDC', 'NON-DDC'])->default('NON-DDC');
            $table->enum('remove_used_cpov', ['Y', 'N'])->default('N');

            $table->string('vehicle_type', 50)->default('vehicle-ctas');

            $table->enum('cpov_widget', ['Y', 'N'])->default('N');
            $table->enum('t3_new_widget', ['Y', 'N'])->default('N');
            $table->enum('new_approach_enable', ['Y', 'N'])->default('N');

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dealers_cta_config_temp');
    }
};
