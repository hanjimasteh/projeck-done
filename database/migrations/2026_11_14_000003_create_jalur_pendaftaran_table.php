<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalur_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedInteger('kuota')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalur_pendaftaran');
    }
};
