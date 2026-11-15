<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informasi_ppdb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ppdb_id')->nullable()->constrained('ppdb')->nullOnDelete();
            $table->string('judul');
            $table->text('isi_konten');
            $table->string('status')->default('draft')->index();
            $table->date('tanggal')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informasi_ppdb');
    }
};
