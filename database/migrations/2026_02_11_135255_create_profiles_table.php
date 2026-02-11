<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @return void
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('moonshine_users')->cascadeOnDelete();

            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();

            $table->boolean('gender')->nullable();
            $table->date('birthday')->nullable();

            $table->string('passport_series_number');
            $table->string('passport_details');

            $table->string('address');
            $table->string('phone_number');
            $table->string('email');

            $table->timestamps();
        });
    }

    /**
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
