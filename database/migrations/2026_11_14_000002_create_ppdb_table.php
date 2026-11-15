<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ppdb', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft')->index();
            $table->timestamps();

            $table->unique('tahun_ajaran');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ppdb');
    }
};
