<?php

use App\Enums\ProductTypes;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();

            $table->string('title');
            $table->enum('type', ProductTypes::values());

            $table->decimal('rate', 5, 2)->default(0.0);
            $table->decimal('limit', 15, 2)->default(0.0);
            $table->dateTimeTz('end_date')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
