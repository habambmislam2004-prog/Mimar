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
        Schema::create('estimation_items', function (Blueprint $table) {
             $table->id();

            $table->foreignId('estimation_id')
                ->constrained('estimations')
                ->cascadeOnDelete();

            $table->foreignId('material_type_id')
                ->constrained('material_types')
                ->restrictOnDelete();

            $table->decimal('calculated_quantity', 12, 3);
            $table->string('unit');

            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('waste_percentage', 5, 2)->default(0);
            $table->decimal('waste_quantity', 12, 3)->default(0);
            $table->decimal('final_quantity', 12, 3)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimation_items');
    }
};
