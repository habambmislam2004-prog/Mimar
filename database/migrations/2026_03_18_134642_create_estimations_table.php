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
        Schema::create('estimations', function (Blueprint $table) {
              $table->id();

            $table->foreignId('user_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('city_id')
                ->constrained('cities')
                ->restrictOnDelete();

            $table->foreignId('estimation_type_id')
                ->constrained('estimation_types')
                ->restrictOnDelete();

            $table->json('input_payload');

            $table->decimal('subtotal_cost', 12, 2)->default(0);
            $table->decimal('waste_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);

            $table->unsignedInteger('estimated_duration_days')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimations');
    }
};
