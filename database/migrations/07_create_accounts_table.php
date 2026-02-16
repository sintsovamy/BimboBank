<?php

use App\Enums\AccountStatuses;
use App\Enums\AccountTypes;
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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('moonshine_users')->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();

            $table->string('account_number', 20);
            $table->enum('type', (array)AccountTypes::class);
            $table->decimal('balance', 18, 2)->default(0.0);
            $table->enum('status', (array)AccountStatuses::class);

            $table->timestampTz('opened_at');
            $table->timestampTz('closed_at')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
