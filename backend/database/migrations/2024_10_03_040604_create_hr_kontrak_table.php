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
        Schema::create('hr_kontrak', function (Blueprint $table) {
            $table->id();
            $table->string('profiles_id');
            $table->string('cabang_id');
            $table->string('no_kontrak');
            $table->string('tipe');
            $table->date('tgl_masuk');
            $table->date('tgl_keluar');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->integer('hari');
            $table->date('tgl_take_out')->nullable();
            $table->date('tgl_make_kontrak')->nullable();
            $table->string('ket');
            $table->string('ket_take_out')->nullable();;
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
        Schema::dropIfExists('hr_kontrak');
    }
};