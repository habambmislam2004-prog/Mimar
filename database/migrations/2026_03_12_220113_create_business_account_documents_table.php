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
        Schema::create('business_account_documents', function (Blueprint $table) {
           $table->id();

            $table->foreignId('business_account_id')
                ->constrained('business_accounts')
                ->cascadeOnDelete();

            $table->string('file_name')->nullable();
            $table->string('file_path');
            $table->string('document_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_account_documents');
    }
};
