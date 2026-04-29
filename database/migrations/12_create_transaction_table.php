<?php

use App\Enums\SourceDestinationTypes;
use App\Enums\TransactionStatusTypes;
use App\Enums\TransactionTypes;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_uuid')->unique()->default(DB::raw('gen_random_uuid()'));

            // internal transactions
            $table->foreignId('source_account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignId('destination_account_id')->nullable()->constrained('accounts')->nullOnDelete();

            // external transactions
            $table->string('external_destination_value', 256)->nullable();
            $table->string('external_destination_bank', 256)->nullable();
            $table->string('external_destination_holder', 256)->nullable();

            $table->string('external_source_bank', 256)->nullable();
            $table->string('external_source_holder', 256)->nullable();

            $table->foreignId('mcc_id')->nullable()->constrained('mcc')->cascadeOnDelete();
            $table->string('merchant_name')->nullable();

            $table->decimal('amount', 19, 4);
            $table->foreignId('currency_id')->constrained('currencies')->cascadeOnDelete();

            $table->enum('transaction_type', TransactionTypes::values());
            $table->enum('status', TransactionStatusTypes::values())->default('pending');

            // timestamps
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('failed_at')->nullable();
            $table->timestampTz('updated_at')->nullable();

            // indexes
            $table->index('source_account_id');
            $table->index('destination_account_id');
            $table->index('created_at');
            $table->index('transaction_type');
            $table->index('external_destination_value');

            $table->index(['source_account_id', 'created_at']);
            $table->index(['destination_account_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
