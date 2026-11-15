<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_ppdb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_id')->constrained('ppdb')->cascadeOnDelete();
            $table->string('tahapan');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['ppdb_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_ppdb');
    }
};
