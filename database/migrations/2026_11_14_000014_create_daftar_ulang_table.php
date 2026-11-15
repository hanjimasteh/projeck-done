<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_ulang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_id')->constrained('ppdb')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftaran')->cascadeOnDelete();
            $table->date('tanggal_daftar_ulang')->nullable();
            $table->string('status')->default('belum')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_ulang');
    }
};
