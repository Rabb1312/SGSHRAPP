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
        Schema::create('hr_rapor', function (Blueprint $table) {
            $table->id();
            $table->string('profiles_id');
            $table->string('grooming')->nullable();
            $table->string('pk')->nullable();
            $table->string('soba')->nullable();
            $table->string('disiplin')->nullable();
            $table->string('admin')->nullable();
            $table->string('mop');
            $table->string('yop');
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
        Schema::dropIfExists('hr_rapor');
    }
};