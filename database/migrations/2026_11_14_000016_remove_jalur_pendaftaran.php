<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop column from pendaftaran if it exists
        if (Schema::hasColumn('pendaftaran', 'jalur_id')) {
            DB::statement('ALTER TABLE `pendaftaran` DROP COLUMN `jalur_id`');
            // Also drop the foreign key constraint if it exists
            try {
                DB::statement('ALTER TABLE `pendaftaran` DROP FOREIGN KEY `pendaftaran_jalur_id_foreign`');
            } catch (\Exception $e) {
                // Constraint may not exist or have different name
            }
        }

        // Drop column from persyaratan if it exists
        if (Schema::hasColumn('persyaratan', 'jalur_id')) {
            DB::statement('ALTER TABLE `persyaratan` DROP COLUMN `jalur_id`');
            try {
                DB::statement('ALTER TABLE `persyaratan` DROP FOREIGN KEY `persyaratan_jalur_id_foreign`');
            } catch (\Exception $e) {
                // Constraint may not exist or have different name
            }
        }

        // Drop the jalur_pendaftaran table entirely
        if (Schema::hasTable('jalur_pendaftaran')) {
            Schema::dropIfExists('jalur_pendaftaran');
        }
    }

    public function down(): void
    {
        // Re-create jalur_pendaftaran table
        Schema::create('jalur_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->unsignedInteger('kuota')->nullable();
            $table->text('keterangan')->nullable();
            $table->boolean('aktif')->default(true)->index();
            $table->timestamps();
        });

        // Re-add jalur_id to pendaftaran
        if (!Schema::hasColumn('pendaftaran', 'jalur_id')) {
            DB::statement('ALTER TABLE `pendaftaran` ADD COLUMN `jalur_id` INT NULL');
            DB::statement('ALTER TABLE `pendaftaran` ADD CONSTRAINT `pendaftaran_jalur_id_foreign` FOREIGN KEY (`jalur_id`) REFERENCES `jalur_pendaftaran` (`id`) ON DELETE RESTRICT');
        }

        // Re-add jalur_id to persyaratan
        if (!Schema::hasColumn('persyaratan', 'jalur_id')) {
            DB::statement('ALTER TABLE `persyaratan` ADD COLUMN `jalur_id` INT NULL');
        }
    }
};