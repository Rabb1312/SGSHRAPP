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
        Schema::create('hr_organisasi', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name',100)->unique();
            $table->string('supplier_address');
            $table->string('supplier_phone',100);
            $table->string('supplier_city',100);
            $table->string('supplier_postalcode',20);
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
        Schema::dropIfExists('hr_organisasi');
    }
};