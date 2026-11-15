<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_id')->constrained('ppdb')->cascadeOnDelete();
            $table->foreignId('calon_siswa_id')->constrained('calon_siswa')->cascadeOnDelete();
            $table->foreignId('jalur_id')->nullable()->constrained('jalur_pendaftaran')->restrictOnDelete();
            $table->string('nomor_pendaftaran')->nullable()->unique();
            $table->dateTime('tanggal_pendaftaran')->nullable();
            $table->foreignId('program_pilihan_1_id')->nullable()->constrained('program')->restrictOnDelete();
            $table->foreignId('program_pilihan_2_id')->nullable()->constrained('program')->nullOnDelete();
            $table->string('status')->default('draft')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['ppdb_id', 'calon_siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran');
    }
};
