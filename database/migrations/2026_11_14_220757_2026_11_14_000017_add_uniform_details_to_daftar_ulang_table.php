<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            $table->string('ukuran_seragam')->nullable()->after('keterangan');
            $table->string('bukti_pembayaran')->nullable()->after('ukuran_seragam');
            $table->text('catatan')->nullable()->after('bukti_pembayaran');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_ulang', function (Blueprint $table) {
            $table->dropColumn(['ukuran_seragam', 'bukti_pembayaran', 'catatan']);
        });
    }
};
