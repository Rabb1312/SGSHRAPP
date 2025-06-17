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
        Schema::create('tempt_report_soba_pk', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_id');
            $table->string('profiles_id');
            $table->string('mop')->nullable();
            $table->string('yop')->nullable();
            $table->string('nama_toko')->nullable();
            $table->string('channel')->nullable();
            $table->string('tb')->nullable();
            $table->string('bb')->nullable();
            $table->string('bmi')->nullable();
            $table->string('klasifikasi')->nullable();
            $table->string('pk')->nullable();
            $table->string('target_siba')->nullable();
            $table->string('target_soba')->nullable();
            $table->string('siba')->nullable();
            $table->string('soba')->nullable();
            $table->string('achv_siba')->nullable();
            $table->string('achv_soba')->nullable();
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
        Schema::dropIfExists('tempt_report_soba_pk');
    }
};
