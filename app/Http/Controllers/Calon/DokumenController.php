<?php

namespace App\Http\Controllers\Calon;

use App\Enums\StatusDokumen;
use App\Http\Controllers\Controller;
use App\Http\Requests\Calon\UploadDokumenRequest;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Services\DokumenService;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DokumenController extends Controller
{
    public function __construct(
        private readonly PendaftaranService $flow,
        private readonly DokumenService $documents,
    ) {}

    public function index(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if (! $pendaftaran) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Lengkapi formulir pendaftaran terlebih dahulu.');
        }

        $nextRoute = $this->flow->nextStepRoute($siswa, $pendaftaran);

        if ($nextRoute !== 'calon.pendaftaran.review') {
            $label = match ($nextRoute) {
                'calon.pendaftaran.data-diri' => 'Data Diri',
                'calon.pendaftaran.orang-tua' => 'Orang Tua/Wali',
                'calon.pendaftaran.program' => 'Pilih Program',
                default => 'langkah sebelumnya',
            };

            return redirect()
                ->route($nextRoute)
                ->with('error', 'Lengkapi langkah "'.$label.'" terlebih dahulu sebelum membuka halaman Dokumen.');
        }

        $jenisDokumenList = $this->documents->jenisDokumenFor($pendaftaran);
        $dokumenUploaded = $this->documents->dokumenFor($pendaftaran);
        $missingRequired = $this->documents->missingRequired($pendaftaran);

        $wajibTotal = $jenisDokumenList->filter(fn ($item) => $item->wajib)->count();
        $wajibTerpenuhi = max(0, $wajibTotal - $missingRequired->count());
        $persenKelengkapan = $wajibTotal > 0 ? (int) round($wajibTerpenuhi / $wajibTotal * 100) : 100;

        return view('calon.pendaftaran.dokumen', [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
            'currentStep' => 'dokumen',
            'steps' => $this->flow->steps($siswa, $pendaftaran),
            'editable' => $this->flow->isEditable($pendaftaran),
            'locked' => $this->flow->isLocked($pendaftaran),
            'closedMessage' => $this->flow->closedMessage(),
            'jenisDokumenList' => $jenisDokumenList,
            'dokumenUploaded' => $dokumenUploaded,
            'missingRequired' => $missingRequired,
            'documentsComplete' => $this->documents->hasCompleteRequired($pendaftaran),
            'wajibTotal' => $wajibTotal,
            'wajibTerpenuhi' => $wajibTerpenuhi,
            'persenKelengkapan' => $persenKelengkapan,
        ]);
    }

    public function store(UploadDokumenRequest $request)
    {
        [, $pendaftaran] = $this->context($request);

        if (! $pendaftaran) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Lengkapi formulir pendaftaran terlebih dahulu.');
        }

        Gate::authorize('create', [Dokumen::class, $pendaftaran]);

        if (! $this->flow->isOpen()) {
            return back()->with('error', $this->flow->closedMessage());
        }

        $jenis = JenisDokumen::findOrFail($request->validated('jenis_dokumen_id'));

        $allowed = $this->documents
            ->jenisDokumenFor($pendaftaran)
            ->contains(fn (JenisDokumen $item) => $item->id === $jenis->id);

        abort_unless($allowed, 403, 'Jenis dokumen tidak tersedia untuk pendaftaran ini.');

        $existing = Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenis->id)
            ->first();

        if ($existing?->status === StatusDokumen::APPROVED) {
            return back()->with('error', 'Dokumen yang sudah disetujui tidak dapat diubah.');
        }

        $this->documents->storeFor($pendaftaran, $jenis, $request->file('file'));

        return back()->with('success', 'Dokumen '.$jenis->nama.' berhasil diunggah.');
    }

    public function preview(Dokumen $dokumen)
    {
        Gate::authorize('view', $dokumen);

        $disk = Storage::disk('local');

        if (! $disk->exists($dokumen->file)) {
            abort(404, 'File dokumen tidak ditemukan.');
        }

        $jenis = $dokumen->jenisDokumen?->nama ?? 'dokumen';
        $extension = pathinfo($dokumen->file, PATHINFO_EXTENSION);
        $filename = Str::slug($jenis).($extension ? '.'.$extension : '');

        return $disk->response(
            $dokumen->file,
            $filename,
            ['Content-Type' => $disk->mimeType($dokumen->file) ?: 'application/octet-stream'],
            'inline'
        );
    }

    public function destroy(Dokumen $dokumen)
    {
        Gate::authorize('delete', $dokumen);

        if (! $this->flow->isOpen()) {
            return back()->with('error', $this->flow->closedMessage());
        }

        $nama = $dokumen->jenisDokumen?->nama ?? 'Dokumen';
        $this->documents->delete($dokumen);

        return back()->with('success', $nama.' berhasil dihapus.');
    }

    private function context(Request $request): array
    {
        $siswa = $this->flow->calonSiswa($request->user());

        return [$siswa, $this->flow->pendaftaranFor($siswa)];
    }
}
