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
        Schema::create('services', function (Blueprint $table) {
             $table->id();

            $table->foreignId('business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->foreignId('category_id')
                ->constrained('categories')
                ->restrictOnDelete();

            $table->foreignId('subcategory_id')
                ->constrained('subcategories')
                ->restrictOnDelete();

            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);

            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();

            $table->foreignId('approved_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('approved_at')->nullable();

            $table->foreignId('rejected_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('rejected_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['business_account_id', 'status']);
            $table->index(['category_id']);
            $table->index(['subcategory_id']);
        });    


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
