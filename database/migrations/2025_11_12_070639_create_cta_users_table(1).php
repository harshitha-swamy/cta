<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cta_users', function (Blueprint $table) {
            $table->id();                          
            $table->string('name');                
            $table->string('email')->unique();     
            $table->string('password'); 
            $table->tinyInteger('role')->comment('1 = Approver, 2 = Developer'); 
            $table->rememberToken();               
            $table->timestamps();                  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cta_users');
    }
};
