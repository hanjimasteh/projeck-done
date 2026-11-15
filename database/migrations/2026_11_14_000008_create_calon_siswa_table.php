<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calon_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('nisn', 10)->nullable()->index();
            $table->string('nama_lengkap')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('asal_sekolah')->nullable();
            $table->string('foto')->nullable();
            $table->string('status')->default('aktif')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calon_siswa');
    }
};
