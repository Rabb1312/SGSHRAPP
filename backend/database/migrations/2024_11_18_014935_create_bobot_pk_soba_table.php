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
        Schema::create('bobot_pk_soba', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('bobot_pk', 5, 2);  // Ubah dari pk_weight
            $table->decimal('bobot_soba', 5, 2); // Ubah dari soba_weight
            $table->boolean('is_active')->default(true);
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bobot_pk_soba');
    }
};