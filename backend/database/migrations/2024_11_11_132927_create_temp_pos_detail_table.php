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
        Schema::create('temp_pos_detail', function (Blueprint $table) {
            $table->id();
            $table->string('cabang_id');
            $table->string('profiles_id');
            $table->string('pk')->nullable();
            $table->string('achv_siba')->nullable();         
            $table->string('achv_soba')->nullable();         
            $table->string('result')->nullable();         
            $table->string('created_by')->nullable();;
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_pos_detail');
    }
};