<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('jenis_dokumen_id')->constrained('jenis_dokumen')->cascadeOnDelete();
            $table->string('file');
            $table->string('status')->default('pending')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['pendaftaran_id', 'jenis_dokumen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokumen');
    }
};
