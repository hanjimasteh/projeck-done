<?php

namespace App\Http\Controllers\Calon;

use App\Http\Controllers\Controller;
use App\Http\Requests\Calon\DataDiriRequest;
use App\Http\Requests\Calon\ProgramRequest;
use App\Http\Requests\Calon\OrangTuaRequest;
use App\Http\Requests\Calon\SubmitPendaftaranRequest;
use App\Models\CalonSiswa;
use App\Models\Program;
use App\Models\Pendaftaran;
use App\Services\DokumenService;
use App\Services\KuotaService;
use App\Services\PendaftaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function __construct(
        private readonly PendaftaranService $flow,
        private readonly DokumenService $documents,
        private readonly KuotaService $kuota,
    ) {}

    public function create()
    {
        return redirect()->route('calon.pendaftaran.data-diri');
    }

    public function dataDiri(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        return view('calon.pendaftaran.data-diri', $this->stepData($siswa, $pendaftaran, 'data-diri'));
    }

    public function saveDataDiri(DataDiriRequest $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if ($pendaftaran) {
            Gate::authorize('update', $pendaftaran);
        }

        if (! $this->flow->isOpen()) {
            return back()->withInput()->with('error', $this->flow->closedMessage());
        }

        $data = $request->safe()->except(['foto']);

        if ($request->hasFile('foto')) {
            if ($siswa->foto) {
                Storage::disk('local')->delete($siswa->foto);
            }

            $data['foto'] = $request->file('foto')->store('calon/foto', 'local');
        }

        $siswa->update($data);

        $this->flow->draftFor($siswa, $this->flow->activePpdb());

        return redirect()
            ->route('calon.pendaftaran.orang-tua')
            ->with('success', 'Data diri berhasil disimpan sebagai draft.');
    }

    public function orangTua(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);
        $siswa->load('orangTua');

        $data = $this->stepData($siswa, $pendaftaran, 'orang-tua');
        $data['orangTua'] = $siswa->orangTua->keyBy('hubungan');

        return view('calon.pendaftaran.orang-tua', $data);
    }

    public function saveOrangTua(OrangTuaRequest $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if ($pendaftaran) {
            Gate::authorize('update', $pendaftaran);
        }

        if (! $this->flow->isOpen()) {
            return back()->withInput()->with('error', $this->flow->closedMessage());
        }

        if (! $this->flow->hasDataDiri($siswa)) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Lengkapi data diri terlebih dahulu.');
        }

        DB::transaction(function () use ($request, $siswa) {
            foreach (['ayah', 'ibu', 'wali'] as $hubungan) {
                $data = $request->input($hubungan);

                $data = is_array($data) ? $data : [];

                if (filled($data['nama'] ?? null)) {
                    $siswa->orangTua()->updateOrCreate(
                        ['hubungan' => $hubungan],
                        [
                            'nama' => $data['nama'],
                            'pekerjaan' => $data['pekerjaan'] ?? null,
                            'no_hp' => $data['no_hp'] ?? null,
                            'alamat' => $data['alamat'] ?? null,
                        ]
                    );

                    continue;
                }

                $siswa->orangTua()->where('hubungan', $hubungan)->delete();
            }
        });

        $this->flow->draftFor($siswa, $this->flow->activePpdb());

        return redirect()
            ->route('calon.pendaftaran.program')
            ->with('success', 'Data orang tua/wali berhasil disimpan sebagai draft.');
    }

    public function program(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        $data = $this->stepData($siswa, $pendaftaran, 'program');
        $data['programList'] = Program::orderBy('nama')->get();
        $data['sisaKuota'] = $this->kuota->petaSisaKuota();

        return view('calon.pendaftaran.program', $data);
    }

    public function saveProgram(ProgramRequest $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if ($pendaftaran) {
            Gate::authorize('update', $pendaftaran);
        }

        if (! $this->flow->isOpen()) {
            return back()->withInput()->with('error', $this->flow->closedMessage());
        }

        if (! $this->flow->hasDataDiri($siswa)) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Lengkapi data diri terlebih dahulu.');
        }

        $pendaftaran = $this->flow->draftFor($siswa, $this->flow->activePpdb());

        $pendaftaran->update([
            'program_pilihan_1_id' => $request->validated('program_pilihan_1_id'),
            'program_pilihan_2_id' => $request->validated('program_pilihan_2_id'),
            'keterangan' => null,
        ]);

        return redirect()
            ->route('calon.pendaftaran.dokumen')
            ->with('success', 'Program pilihan berhasil disimpan sebagai draft.');
    }

    public function review(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);
        $siswa->load('orangTua');

        if ($pendaftaran) {
            $pendaftaran->load(['programPilihan1', 'programPilihan2', 'ppdb']);
        }

        $data = $this->stepData($siswa, $pendaftaran, 'review');
        $data['complete'] = $this->flow->isComplete($siswa, $pendaftaran);
        $data['nextRoute'] = $this->flow->nextStepRoute($siswa, $pendaftaran);
        $data['jenisDokumenList'] = $this->documents->jenisDokumenFor($pendaftaran);
        $data['dokumenUploaded'] = $this->documents->dokumenFor($pendaftaran);
        $data['missingRequired'] = $this->documents->missingRequired($pendaftaran);
        $data['documentsComplete'] = $this->documents->hasCompleteRequired($pendaftaran);
        $data['canSubmit'] = $data['complete'] && $data['documentsComplete'];

        return view('calon.pendaftaran.review', $data);
    }

    public function submit(SubmitPendaftaranRequest $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if (! $pendaftaran) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Lengkapi formulir pendaftaran terlebih dahulu.');
        }

        Gate::authorize('submit', $pendaftaran);

        if (! $this->flow->isOpen()) {
            return back()->with('error', $this->flow->closedMessage());
        }

        $nextRoute = $this->flow->nextStepRoute($siswa, $pendaftaran);

        if ($nextRoute !== 'calon.pendaftaran.review') {
            return redirect()
                ->route($nextRoute)
                ->with('error', 'Lengkapi seluruh langkah formulir sebelum mengirim pendaftaran.');
        }

        if (! $this->documents->hasCompleteRequired($pendaftaran)) {
            $missing = $this->documents->missingRequired($pendaftaran)
                ->pluck('nama')
                ->join(', ');

            return redirect()
                ->route('calon.pendaftaran.dokumen')
                ->with('error', 'Dokumen wajib belum lengkap: '.$missing.'. Unggah dokumen tersebut sebelum mengirim pendaftaran.');
        }

        if (! $this->kuotaTersedia($pendaftaran)) {
            return redirect()
                ->route('calon.pendaftaran.program')
                ->with('error', 'Kuota program yang Anda pilih sudah penuh. Silakan pilih program yang masih tersedia.');
        }

        $submitted = $this->flow->submit($pendaftaran);

        return redirect()
            ->route('calon.pendaftaran.review')
            ->with('success', 'Pendaftaran berhasil dikirim dengan nomor '.$submitted->nomor_pendaftaran.'.');
    }

    public function show(Pendaftaran $pendaftaran)
    {
        Gate::authorize('view', $pendaftaran);

        return redirect()->route('calon.pendaftaran.review');
    }

    public function status(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if ($pendaftaran) {
            $pendaftaran->load(['programPilihan1', 'programPilihan2', 'pengumuman']);
        }

        return view('calon.status.index', [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
        ]);
    }

    public function hasilSeleksi(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if ($pendaftaran) {
            $pendaftaran->load(['pengumuman', 'seleksi.programDiterima']);
        }

        return view('calon.hasil.index', [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
        ]);
    }

    public function cetakBukti(Request $request)
    {
        [$siswa, $pendaftaran] = $this->context($request);

        if (! $pendaftaran || ! $pendaftaran->nomor_pendaftaran) {
            return redirect()
                ->route('calon.pendaftaran.data-diri')
                ->with('error', 'Anda belum melakukan pendaftaran.');
        }

        $pendaftaran->load(['programPilihan1', 'programPilihan2', 'ppdb']);
        $siswa->load('orangTua');

        return view('calon.pendaftaran.cetak', compact('pendaftaran', 'siswa'));
    }

    private function context(Request $request): array
    {
        $siswa = $this->flow->calonSiswa($request->user());

        return [$siswa, $this->flow->pendaftaranFor($siswa)];
    }

    /**
     * Apakah masih ada kursi kosong pada program yang dipilih pendaftar.
     */
    private function kuotaTersedia(Pendaftaran $pendaftaran): bool
    {
        $programs = collect([
            $pendaftaran->program_pilihan_1_id,
            $pendaftaran->program_pilihan_2_id,
        ])->filter()->map(fn ($id) => Program::find($id))->filter();

        if ($programs->isEmpty()) {
            return false;
        }

        return $programs->contains(fn (Program $program) => ! $this->kuota->penuh($program));
    }

    private function stepData(CalonSiswa $siswa, ?Pendaftaran $pendaftaran, string $currentStep): array
    {
        return [
            'siswa' => $siswa,
            'pendaftaran' => $pendaftaran,
            'currentStep' => $currentStep,
            'steps' => $this->flow->steps($siswa, $pendaftaran),
            'editable' => $this->flow->isEditable($pendaftaran),
            'locked' => $this->flow->isLocked($pendaftaran),
            'closedMessage' => $this->flow->closedMessage(),
        ];
    }
}