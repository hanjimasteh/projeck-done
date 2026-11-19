<?php

namespace Tests\Feature;

use App\Enums\StatusDokumen;
use App\Enums\StatusPendaftaran;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
        Storage::fake('local');

        Carbon::setTestNow('2027-03-15 09:00:00');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_pendaftar_dapat_mengunggah_dokumen_dan_file_tersimpan_di_storage_privat(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);
        $jenis = $this->jenis('Kartu Keluarga');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $jenis->id,
                'file' => UploadedFile::fake()->create('kartu-keluarga.pdf', 200, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $dokumen = Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenis->id)
            ->firstOrFail();

        $this->assertSame(StatusDokumen::PENDING, $dokumen->status);
        $this->assertStringStartsWith('dokumen/'.$pendaftaran->ppdb_id.'/'.$pendaftaran->id.'/', $dokumen->file);
        $this->assertStringNotContainsString('public', $dokumen->file);
        Storage::disk('local')->assertExists($dokumen->file);
        $this->assertFileDoesNotExist(public_path('storage/'.$dokumen->file));

        $this->actingAs($user)
            ->get('/calon/dokumen/'.$dokumen->id.'/preview')
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_validasi_menolak_format_dan_ukuran_yang_tidak_sesuai(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);
        $pasFoto = $this->jenis('Pas Foto');
        $kartuKeluarga = $this->jenis('Kartu Keluarga');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $pasFoto->id,
                'file' => UploadedFile::fake()->create('pas-foto.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->post('/calon/pendaftaran/dokumen', [
            'jenis_dokumen_id' => $pasFoto->id,
            'file' => UploadedFile::fake()->create('pas-foto.exe', 100, 'application/octet-stream'),
        ])->assertSessionHasErrors('file');

        $this->post('/calon/pendaftaran/dokumen', [
            'jenis_dokumen_id' => $kartuKeluarga->id,
            'file' => UploadedFile::fake()->create('kartu-keluarga.pdf', 3000, 'application/pdf'),
        ])->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('dokumen', ['pendaftaran_id' => $pendaftaran->id]);
    }

    public function test_pendaftar_tidak_bisa_preview_atau_menghapus_dokumen_milik_pendaftar_lain(): void
    {
        $pemilik = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($pemilik);
        $dokumen = $this->upload($pemilik, $this->jenis('Kartu Keluarga'));

        $lain = $this->createPendaftar();
        $this->completeDraft($lain, [
            'nik' => '3273010101090088',
            'nisn' => '9988776655',
            'nama_lengkap' => 'Pendaftar Lain',
        ]);

        $this->actingAs($lain)
            ->get('/calon/dokumen/'.$dokumen->id.'/preview')
            ->assertForbidden();

        $this->actingAs($lain)
            ->delete('/calon/dokumen/'.$dokumen->id)
            ->assertForbidden();

        $this->assertDatabaseHas('dokumen', ['id' => $dokumen->id]);
        Storage::disk('local')->assertExists($dokumen->file);

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/admin/dokumen/'.$dokumen->id.'/preview')
            ->assertOk();

        $this->assertSame($pendaftaran->id, $dokumen->pendaftaran_id);
    }

    public function test_tidak_bisa_submit_jika_dokumen_wajib_belum_lengkap_dan_bisa_setelah_lengkap(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/review')
            ->assertOk()
            ->assertSee('Dokumen wajib belum lengkap.');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.dokumen'))
            ->assertSessionHas('error');

        $pendaftaran->refresh();
        $this->assertSame(StatusPendaftaran::DRAFT, $pendaftaran->status);
        $this->assertNull($pendaftaran->nomor_pendaftaran);

        $this->uploadRequiredDokumen($user);

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.review'))
            ->assertSessionHas('success');

        $pendaftaran->refresh();
        $this->assertSame(StatusPendaftaran::SUBMITTED, $pendaftaran->status);
        $this->assertNotNull($pendaftaran->nomor_pendaftaran);
    }

    public function test_dokumen_terkunci_saat_pendaftaran_sudah_disubmit(): void
    {
        $user = $this->createPendaftar();
        $this->completeDraft($user);
        $dokumen = $this->upload($user, $this->jenis('Kartu Keluarga'));

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertSessionHas('success');

        $this->actingAs($user)
            ->get('/calon/pendaftaran/dokumen')
            ->assertOk()
            ->assertSee('Dokumen hanya dapat dilihat.');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $this->jenis('Sertifikat Prestasi')->id,
                'file' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();

        $this->actingAs($user)
            ->delete('/calon/dokumen/'.$dokumen->id)
            ->assertForbidden();

        $this->assertDatabaseHas('dokumen', ['id' => $dokumen->id]);
        Storage::disk('local')->assertExists($dokumen->file);

        $this->actingAs($user)
            ->get('/calon/dokumen/'.$dokumen->id.'/preview')
            ->assertOk();
    }

    public function test_dokumen_terkunci_di_luar_jadwal_pendaftaran(): void
    {
        $user = $this->createPendaftar();
        $this->completeDraft($user);
        $dokumen = $this->upload($user, $this->jenis('Kartu Keluarga'));

        Carbon::setTestNow('2027-07-10 09:00:00');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $this->jenis('Sertifikat Prestasi')->id,
                'file' => UploadedFile::fake()->create('sertifikat.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->actingAs($user)
            ->delete('/calon/dokumen/'.$dokumen->id)
            ->assertSessionHas('error');

        $this->assertDatabaseHas('dokumen', ['id' => $dokumen->id]);
        Storage::disk('local')->assertExists($dokumen->file);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/dokumen')
            ->assertOk()
            ->assertSee('Formulir hanya dapat dilihat.');
    }

    public function test_dokumen_rejected_dapat_diupload_ulang_dan_status_kembali_pending(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);
        $jenis = $this->jenis('Kartu Keluarga');
        $dokumen = $this->uploadRequiredDokumen($user)->get($jenis->id);
        $oldPath = $dokumen->file;

        $dokumen->update([
            'status' => StatusDokumen::REJECTED,
            'keterangan' => 'Hasil scan buram, silakan unggah ulang.',
        ]);

        $pendaftaran->update([
            'status' => StatusPendaftaran::REJECTED,
            'keterangan' => 'Perbaiki dokumen yang ditolak.',
        ]);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/dokumen')
            ->assertOk()
            ->assertSee('Hasil scan buram, silakan unggah ulang.');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $jenis->id,
                'file' => UploadedFile::fake()->create('kartu-keluarga-baru.pdf', 300, 'application/pdf'),
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $dokumen->refresh();

        $this->assertSame(StatusDokumen::PENDING, $dokumen->status);
        $this->assertNull($dokumen->keterangan);
        $this->assertNotSame($oldPath, $dokumen->file);
        Storage::disk('local')->assertMissing($oldPath);
        Storage::disk('local')->assertExists($dokumen->file);
    }

    public function test_dokumen_approved_terkunci_dan_tidak_bisa_diganti_atau_dihapus(): void
    {
        $user = $this->createPendaftar();
        $this->completeDraft($user);
        $dokumen = $this->upload($user, $this->jenis('Kartu Keluarga'));
        $dokumen->update(['status' => StatusDokumen::APPROVED]);
        $oldPath = $dokumen->file;

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $dokumen->jenis_dokumen_id,
                'file' => UploadedFile::fake()->create('pengganti.pdf', 100, 'application/pdf'),
            ])
            ->assertSessionHas('error');

        $this->actingAs($user)
            ->delete('/calon/dokumen/'.$dokumen->id)
            ->assertForbidden();

        $dokumen->refresh();

        $this->assertSame($oldPath, $dokumen->file);
        Storage::disk('local')->assertExists($oldPath);
    }

    public function test_menghapus_dokumen_menghapus_file_fisiknya(): void
    {
        $user = $this->createPendaftar();
        $this->completeDraft($user);
        $dokumen = $this->upload($user, $this->jenis('Kartu Keluarga'));
        $path = $dokumen->file;

        $this->actingAs($user)
            ->delete('/calon/dokumen/'.$dokumen->id)
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('dokumen', ['id' => $dokumen->id]);
        Storage::disk('local')->assertMissing($path);
    }

    private function createPendaftar(): User
    {
        return User::factory()->create();
    }

    private function jenis(string $nama): JenisDokumen
    {
        return JenisDokumen::where('nama', $nama)->firstOrFail();
    }

    private function upload(User $user, JenisDokumen $jenis, ?UploadedFile $file = null): Dokumen
    {
        $file ??= str_contains(strtolower($jenis->format_file), 'jpg')
            ? UploadedFile::fake()->image('dokumen-'.$jenis->id.'.jpg')
            : UploadedFile::fake()->create('dokumen-'.$jenis->id.'.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/dokumen', [
                'jenis_dokumen_id' => $jenis->id,
                'file' => $file,
            ])
            ->assertRedirect();

        $pendaftaran = $this->pendaftaranFor($user);

        return Dokumen::where('pendaftaran_id', $pendaftaran->id)
            ->where('jenis_dokumen_id', $jenis->id)
            ->firstOrFail();
    }

    private function uploadRequiredDokumen(User $user): Collection
    {
        return JenisDokumen::where('wajib', true)
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (JenisDokumen $jenis) => [$jenis->id => $this->upload($user, $jenis)]);
    }

    private function dataDiriPayload(array $overrides = []): array
    {
        return array_merge([
            'nik' => '3273010101090002',
            'nisn' => '1234567891',
            'nama_lengkap' => 'Budi Santoso',
            'tempat_lahir' => 'Bandung',
            'tanggal_lahir' => '2009-05-10',
            'jenis_kelamin' => 'L',
            'alamat' => 'Jl. Merdeka No. 10, Bandung',
            'no_hp' => '081234567891',
            'asal_sekolah' => 'SMP Negeri 2 Bandung',
        ], $overrides);
    }

    private function orangTuaPayload(): array
    {
        return [
            'ayah' => [
                'nama' => 'Ayah Budi',
                'pekerjaan' => 'Karyawan',
                'no_hp' => '081200000001',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
            ],
            'ibu' => [
                'nama' => 'Ibu Budi',
                'pekerjaan' => 'Guru',
                'no_hp' => '081200000002',
                'alamat' => 'Jl. Merdeka No. 10, Bandung',
            ],
        ];
    }

    private function programPayload(): array
    {
        return [
            'program_pilihan_1_id' => Program::where('kode', 'PPLG')->value('id'),
            'program_pilihan_2_id' => Program::where('kode', 'TJKT')->value('id'),
        ];
    }

    private function completeDraft(User $user, array $dataDiriOverrides = []): Pendaftaran
    {
        $this->actingAs($user)
            ->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload($dataDiriOverrides))
            ->assertRedirect(route('calon.pendaftaran.orang-tua'));

        $this->post('/calon/pendaftaran/orang-tua', $this->orangTuaPayload())
            ->assertRedirect(route('calon.pendaftaran.program'));

        $this->post('/calon/pendaftaran/program', $this->programPayload())
            ->assertRedirect(route('calon.pendaftaran.dokumen'));

        return $this->pendaftaranFor($user);
    }

    private function pendaftaranFor(User $user): Pendaftaran
    {
        return Pendaftaran::whereHas('calonSiswa', fn ($query) => $query->where('user_id', $user->id))
            ->firstOrFail();
    }
}
