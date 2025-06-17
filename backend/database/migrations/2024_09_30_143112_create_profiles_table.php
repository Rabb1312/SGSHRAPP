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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('users_id');
            $table->string('cabang_id');
            $table->string('kode_cabang')->nullable(); 
            $table->string('kode_lokasi')->nullable(); 
            $table->string('jabatan_id');
            $table->string('unit_id');
            $table->string('nik')->nullable();
            $table->string('profile_name');
            $table->string('profile_gender');
            $table->string('profile_address');
            $table->string('postalcode');
            $table->string('email');
            $table->string('profile_phones')->unique();
            $table->string('profile_phones2')->nullable(); 
            $table->string('identity_number');
            $table->string('birthplace');
            $table->string('birthdate');
            $table->string('marital_status');
            $table->string('nama_ayah')->nullable();;
            $table->string('nama_ibu');
            $table->string('nama_pasangan')->nullable();;
            $table->string('nama_anak')->nullable();;
            $table->string('blood_type');
            $table->string('join_date');
            $table->string('religion');
            $table->string('education_status');
            $table->string('is_active')->default('Aktif');
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
        Schema::dropIfExists('profiles');
    }
};