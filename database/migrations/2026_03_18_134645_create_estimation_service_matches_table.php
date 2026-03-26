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
        Schema::create('estimation_service_matches', function (Blueprint $table) {
             $table->id();

            $table->foreignId('estimation_id')
                ->constrained('estimations')
                ->cascadeOnDelete();

            $table->foreignId('service_id')->nullable()
                ->constrained('services')
                ->nullOnDelete();

            $table->foreignId('business_account_id')->nullable()
                ->constrained('business_accounts')
                ->nullOnDelete();

            $table->string('match_type')->nullable();
            $table->decimal('score', 8, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimation_service_matches');
    }
};
