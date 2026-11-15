<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orang_tua', function (Blueprint $table) {
            $table->id();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->enum('hubungan', ['ayah', 'ibu', 'wali']);
            $table->string('nama');
            $table->string('pekerjaan')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orang_tua');
    }
};
