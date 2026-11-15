<?php

namespace App\Services;

use App\Enums\StatusDokumen;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\Persyaratan;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class DokumenService
{
    public function jenisDokumenFor(?Pendaftaran $pendaftaran): Collection
    {
        $persyaratan = Persyaratan::query()
            ->orderByDesc('wajib')
            ->orderBy('nama')
            ->get();

        $jenis = JenisDokumen::query()
            ->when(
                $persyaratan->isNotEmpty(),
                fn ($query) => $query->whereIn('nama', $persyaratan->pluck('nama')->unique()->values())
            )
            ->orderByDesc('wajib')
            ->orderBy('kategori')
            ->orderBy('nama')
            ->get();

        $wajib = $persyaratan->pluck('wajib', 'nama');

        return $jenis->each(function (JenisDokumen $item) use ($wajib) {
            if ($wajib->has($item->nama)) {
                $item->wajib = (bool) $wajib->get($item->nama);
            }
        });
    }

    public function dokumenFor(?Pendaftaran $pendaftaran): Collection
    {
        if (! $pendaftaran) {
            return collect();
        }

        return Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->get()
            ->keyBy('jenis_dokumen_id');
    }

    public function missingRequired(?Pendaftaran $pendaftaran): Collection
    {
        $dokumen = $this->dokumenFor($pendaftaran);

        return $this->jenisDokumenFor($pendaftaran)
            ->where('wajib', true)
            ->filter(function (JenisDokumen $jenis) use ($dokumen) {
                $uploaded = $dokumen->get($jenis->id);

                return ! $uploaded || $uploaded->status === StatusDokumen::REJECTED;
            })
            ->values();
    }

    public function hasCompleteRequired(?Pendaftaran $pendaftaran): bool
    {
        return $pendaftaran !== null && $this->missingRequired($pendaftaran)->isEmpty();
    }

    public function directory(Pendaftaran $pendaftaran): string
    {
        return 'dokumen/'.$pendaftaran->ppdb_id.'/'.$pendaftaran->id;
    }

    public function allowedFormats(JenisDokumen $jenisDokumen): array
    {
        return collect(explode(',', strtolower($jenisDokumen->format_file)))
            ->map(fn (string $format) => trim($format))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function allowedExtensions(JenisDokumen $jenisDokumen): array
    {
        return collect($this->allowedFormats($jenisDokumen))
            ->flatMap(function (string $format) {
                return match ($format) {
                    'jpg' => ['jpg', 'jpeg'],
                    'jpeg' => ['jpeg', 'jpg'],
                    default => [$format],
                };
            })
            ->unique()
            ->values()
            ->all();
    }

    public function allowedMimeTypes(JenisDokumen $jenisDokumen): array
    {
        $mapping = [
            'pdf' => ['application/pdf'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
            'gif' => ['image/gif'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'zip' => ['application/zip', 'application/x-zip-compressed'],
        ];

        return collect($this->allowedExtensions($jenisDokumen))
            ->flatMap(function (string $extension) use ($mapping) {
                return $mapping[$extension]
                    ?? MimeTypes::getDefault()->getMimeTypes($extension);
            })
            ->unique()
            ->values()
            ->all();
    }

    public function formatLabel(JenisDokumen $jenisDokumen): string
    {
        return collect($this->allowedFormats($jenisDokumen))
            ->map(fn (string $format) => strtoupper($format))
            ->join(', ');
    }

    public function sizeLabel(int $kilobytes): string
    {
        if ($kilobytes >= 1024 && $kilobytes % 1024 === 0) {
            return intdiv($kilobytes, 1024).' MB';
        }

        return $kilobytes.' KB';
    }

    public function storeFor(Pendaftaran $pendaftaran, JenisDokumen $jenisDokumen, UploadedFile $file): Dokumen
    {
        $directory = $this->directory($pendaftaran);
        $newPath = null;

        try {
            return DB::transaction(function () use ($pendaftaran, $jenisDokumen, $file, $directory, &$newPath) {
                $existing = Dokumen::where('pendaftaran_id', $pendaftaran->id)
                    ->where('jenis_dokumen_id', $jenisDokumen->id)
                    ->lockForUpdate()
                    ->first();

                $oldPath = $existing?->file;
                $newPath = $file->storeAs($directory, $file->hashName(), 'local');

                if (! $newPath) {
                    throw new \RuntimeException('Gagal menyimpan dokumen.');
                }

                $dokumen = $existing ?? new Dokumen([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_dokumen_id' => $jenisDokumen->id,
                ]);

                $dokumen->fill([
                    'file' => $newPath,
                    'status' => StatusDokumen::PENDING,
                    'keterangan' => null,
                ]);
                $dokumen->save();

                if ($oldPath && $oldPath !== $newPath) {
                    Storage::disk('local')->delete($oldPath);
                }

                return $dokumen;
            });
        } catch (Throwable $exception) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }

            throw $exception;
        }
    }

    public function delete(Dokumen $dokumen): void
    {
        DB::transaction(function () use ($dokumen) {
            $path = $dokumen->file;
            $dokumen->delete();

            if ($path) {
                Storage::disk('local')->delete($path);
            }
        });
    }
}
