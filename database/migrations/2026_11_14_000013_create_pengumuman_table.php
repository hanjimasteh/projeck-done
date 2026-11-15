<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_id')->constrained('ppdb')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftaran')->cascadeOnDelete();
            $table->dateTime('tanggal_pengumuman')->nullable();
            $table->enum('hasil', ['diterima', 'tidak_diterima']);
            $table->text('keterangan')->nullable();
            $table->boolean('dipublikasikan')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumuman');
    }
};
