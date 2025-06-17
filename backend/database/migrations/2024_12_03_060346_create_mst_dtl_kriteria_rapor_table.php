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
        Schema::create('mst_dtl_kriteria_rapor', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('jabatan_id');
            $table->string('kriteria_id');
            $table->enum('is_active', ['0', '1'])->default('1');
            $table->softDeletes();
            $table->timestamps();
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_dtl_kriteria_rapor');
    }
};