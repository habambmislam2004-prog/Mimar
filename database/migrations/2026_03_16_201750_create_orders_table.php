<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
               $table->id();

            $table->foreignId('service_id')
                ->constrained('services')
                ->cascadeOnDelete();

            $table->foreignId('sender_business_account_id')
                ->constrained('business_accounts')
                ->restrictOnDelete();

            $table->foreignId('receiver_business_account_id')
                ->constrained('business_accounts')
                ->restrictOnDelete();

            $table->unsignedInteger('quantity')->default(1);
            $table->text('details')->nullable();
            $table->timestamp('needed_at')->nullable();

            $table->string('status')->default(OrderStatus::PENDING->value);

            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['service_id']);
            $table->index(['sender_business_account_id']);
            $table->index(['receiver_business_account_id']);
            $table->index(['status']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
