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
        Schema::create('profiles_detail', function (Blueprint $table) {
            $table->id();
            $table->string('profiles_id');
            $table->string('masa_kerja_ke');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('hari');
            $table->string('sts_take_out')->nullable();
            $table->string('ket');
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
        Schema::dropIfExists('profiles_detail');
    }
};