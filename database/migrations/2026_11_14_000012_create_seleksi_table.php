<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftaran')->cascadeOnDelete();
            $table->foreignId('program_diterima_id')->nullable()->constrained('program')->nullOnDelete();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->unsignedInteger('peringkat')->nullable();
            $table->string('status')->default('pending')->index();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seleksi');
    }
};
