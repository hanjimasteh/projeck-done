<?php

namespace Tests\Feature;

use App\Enums\StatusPendaftaran;
use App\Models\CalonSiswa;
use App\Models\JenisDokumen;
use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PendaftaranTest extends TestCase
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

    public function test_pendaftaran_disimpan_sebagai_draft_lalu_bisa_disubmit(): void
    {
        $user = $this->createPendaftar();

        $this->actingAs($user)
            ->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload())
            ->assertRedirect(route('calon.pendaftaran.orang-tua'));

        $pendaftaran = $this->pendaftaranFor($user);
        $this->assertSame(StatusPendaftaran::DRAFT, $pendaftaran->status);
        $this->assertNull($pendaftaran->nomor_pendaftaran);

        $this->post('/calon/pendaftaran/orang-tua', $this->orangTuaPayload())
            ->assertRedirect(route('calon.pendaftaran.program'));

        $this->post('/calon/pendaftaran/program', $this->programPayload())
            ->assertRedirect(route('calon.pendaftaran.dokumen'));

        $this->uploadRequiredDokumen($user);

        $this->get('/calon/pendaftaran/review')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertSee('Ayah Budi')
            ->assertSee('Pengembangan Perangkat Lunak dan Gim');

        $this->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.review'))
            ->assertSessionHas('success');

        $pendaftaran->refresh();

        $this->assertSame(StatusPendaftaran::SUBMITTED, $pendaftaran->status);
        $this->assertSame('PPDB-2027-0002', $pendaftaran->nomor_pendaftaran);
        $this->assertNotNull($pendaftaran->tanggal_pendaftaran);
    }

    public function test_nik_harus_16_digit_dan_nisn_harus_10_digit(): void
    {
        $user = $this->createPendaftar();

        $response = $this->actingAs($user)->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload([
            'nik' => '123',
            'nisn' => 'abc',
        ]));

        $response->assertSessionHasErrors(['nik', 'nisn']);

        $this->assertStringContainsString('16 digit', session('errors')->first('nik'));
        $this->assertStringContainsString('10 digit', session('errors')->first('nisn'));
        $this->assertDatabaseMissing('calon_siswa', [
            'user_id' => $user->id,
            'nik' => '123',
        ]);
    }

    public function test_orang_tua_minimal_satu_harus_terisi(): void
    {
        $user = $this->createPendaftar();

        $this->actingAs($user)->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload());

        $this->post('/calon/pendaftaran/orang-tua', [
            'ayah' => ['nama' => '', 'pekerjaan' => '', 'no_hp' => '', 'alamat' => ''],
            'ibu' => ['nama' => '', 'pekerjaan' => '', 'no_hp' => '', 'alamat' => ''],
            'wali' => ['nama' => '', 'pekerjaan' => '', 'no_hp' => '', 'alamat' => ''],
        ])->assertSessionHasErrors(['ayah.nama']);

        $this->assertSame(0, $user->calonSiswa()->first()->orangTua()->count());
    }

    public function test_pilihan_2_tidak_boleh_sama_dengan_pilihan_1(): void
    {
        $user = $this->createPendaftar();

        $this->actingAs($user)->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload());
        $this->post('/calon/pendaftaran/orang-tua', $this->orangTuaPayload());

        $program = Program::where('kode', 'PPLG')->firstOrFail();

        $response = $this->post('/calon/pendaftaran/program', [
            'program_pilihan_1_id' => $program->id,
            'program_pilihan_2_id' => $program->id,
        ]);

        $response->assertSessionHasErrors(['program_pilihan_2_id']);
        $this->assertStringContainsString(
            'tidak boleh sama',
            session('errors')->first('program_pilihan_2_id')
        );
        $this->assertNull($this->pendaftaranFor($user)->program_pilihan_1_id);
    }

    public function test_tidak_bisa_menyimpan_atau_submit_di_luar_jadwal(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);

        Carbon::setTestNow('2027-07-10 09:00:00');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload([
                'nama_lengkap' => 'Nama Saat Pendaftaran Tutup',
            ]))
            ->assertSessionHas('error');

        $this->assertNotSame(
            'Nama Saat Pendaftaran Tutup',
            $user->calonSiswa()->first()->nama_lengkap
        );

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertSessionHas('error');

        $pendaftaran->refresh();
        $this->assertSame(StatusPendaftaran::DRAFT, $pendaftaran->status);
        $this->assertNull($pendaftaran->nomor_pendaftaran);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/review')
            ->assertOk()
            ->assertSee('Formulir hanya dapat dilihat');

        $this->actingAs($user)
            ->get('/calon/pendaftaran/data-diri')
            ->assertOk()
            ->assertSee('Formulir hanya dapat dilihat');
    }

    public function test_satu_pendaftar_hanya_punya_satu_pendaftaran_per_periode(): void
    {
        $user = $this->createPendaftar();

        $this->completeDraft($user);

        $this->actingAs($user)->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload());
        $this->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload());

        $this->assertSame(1, $this->pendaftaranCount($user));

        $this->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.review'));

        $this->assertSame(1, $this->pendaftaranCount($user));
    }

    public function test_pendaftar_tidak_bisa_melihat_pendaftaran_lain(): void
    {
        $pemilikLain = $this->createPendaftar();
        $pendaftaranLain = $this->completeDraft($pemilikLain, [
            'nik' => '3273010101090099',
            'nisn' => '9876543210',
            'nama_lengkap' => 'Pendaftar Lain',
        ]);

        $user = $this->createPendaftar();
        $pendaftaranSendiri = $this->completeDraft($user);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/'.$pendaftaranLain->id)
            ->assertForbidden();

        $this->actingAs($user)
            ->get('/calon/pendaftaran/'.$pendaftaranSendiri->id)
            ->assertRedirect(route('calon.pendaftaran.review'));
    }

    public function test_formulir_terkunci_setelah_submit_dan_terbuka_lagi_saat_rejected(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.review'));

        $nomorAwal = $pendaftaran->fresh()->nomor_pendaftaran;

        $this->actingAs($user)
            ->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload([
                'nama_lengkap' => 'Nama Setelah Terkunci',
            ]))
            ->assertForbidden();

        $this->assertNotSame('Nama Setelah Terkunci', $user->calonSiswa()->first()->nama_lengkap);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/data-diri')
            ->assertOk()
            ->assertSee('terkunci');

        $pendaftaran->update([
            'status' => StatusPendaftaran::REJECTED,
            'keterangan' => 'Perbaiki NIK dan NISN.',
        ]);

        $this->actingAs($user)
            ->get('/calon/pendaftaran/review')
            ->assertOk()
            ->assertSee('Perbaiki NIK dan NISN.')
            ->assertSee('Kirim Ulang Pendaftaran');

        $this->actingAs($user)
            ->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload([
                'nama_lengkap' => 'Nama Hasil Revisi',
            ]))
            ->assertRedirect(route('calon.pendaftaran.orang-tua'));

        $this->assertSame('Nama Hasil Revisi', $user->calonSiswa()->first()->nama_lengkap);

        $this->actingAs($user)
            ->post('/calon/pendaftaran/submit', ['pernyataan' => 1])
            ->assertRedirect(route('calon.pendaftaran.review'))
            ->assertSessionHas('success');

        $pendaftaran->refresh();

        $this->assertSame(StatusPendaftaran::SUBMITTED, $pendaftaran->status);
        $this->assertSame($nomorAwal, $pendaftaran->nomor_pendaftaran);
        $this->assertSame('Perbaiki NIK dan NISN.', $pendaftaran->keterangan);
    }

    public function test_nomor_pendaftaran_unik_dan_berurutan(): void
    {
        $userA = $this->createPendaftar();
        $this->completeDraft($userA);
        $this->actingAs($userA)->post('/calon/pendaftaran/submit', ['pernyataan' => 1]);

        $userB = $this->createPendaftar();
        $this->completeDraft($userB, [
            'nik' => '3273010101090077',
            'nisn' => '1122334455',
            'nama_lengkap' => 'Pendaftar Kedua',
        ]);
        $this->actingAs($userB)->post('/calon/pendaftaran/submit', ['pernyataan' => 1]);

        $nomorA = $this->pendaftaranFor($userA)->nomor_pendaftaran;
        $nomorB = $this->pendaftaranFor($userB)->nomor_pendaftaran;

        $this->assertSame('PPDB-2027-0002', $nomorA);
        $this->assertSame('PPDB-2027-0003', $nomorB);
        $this->assertNotSame($nomorA, $nomorB);
        $this->assertMatchesRegularExpression('/^PPDB-2027-\d{4}$/', $nomorA);
    }

    public function test_foto_disimpan_di_storage_privat(): void
    {
        Storage::fake('local');

        $user = $this->createPendaftar();

        $this->actingAs($user)->post('/calon/pendaftaran/data-diri', $this->dataDiriPayload([
            'foto' => UploadedFile::fake()->image('foto-calon.jpg'),
        ]))->assertRedirect(route('calon.pendaftaran.orang-tua'));

        $siswa = CalonSiswa::where('user_id', $user->id)->firstOrFail();

        $this->assertNotNull($siswa->foto);
        $this->assertStringStartsWith('calon/foto/', $siswa->foto);
        Storage::disk('local')->assertExists($siswa->foto);
    }

    public function test_halaman_dashboard_status_hasil_dan_cetak_dapat_dibuka(): void
    {
        $user = $this->createPendaftar();
        $this->completeDraft($user);

        $this->actingAs($user)->post('/calon/pendaftaran/submit', ['pernyataan' => 1]);

        $nomor = $this->pendaftaranFor($user)->nomor_pendaftaran;

        $this->actingAs($user)
            ->get('/calon/dashboard')
            ->assertOk()
            ->assertSee('Diajukan')
            ->assertSee('Pengajuan')
            ->assertSee($nomor);

        $this->actingAs($user)
            ->get('/calon/status')
            ->assertOk()
            ->assertSee($nomor)
            ->assertSee('Pengembangan Perangkat Lunak dan Gim');

        $this->actingAs($user)
            ->get('/calon/hasil')
            ->assertOk()
            ->assertSee('Hasil Belum Diumumkan');

        $this->actingAs($user)
            ->get('/calon/pendaftaran/cetak')
            ->assertOk()
            ->assertSee($nomor)
            ->assertSee('Ayah Budi');
    }

    public function test_halaman_cetak_mengarahkan_ke_formulir_jika_belum_mendaftar(): void
    {
        $user = $this->createPendaftar();

        $this->actingAs($user)
            ->get('/calon/pendaftaran/cetak')
            ->assertRedirect(route('calon.pendaftaran.data-diri'))
            ->assertSessionHas('error');
    }

    public function test_hasil_seleksi_yang_dipublikasikan_dapat_dilihat(): void
    {
        $user = $this->createPendaftar();
        $pendaftaran = $this->completeDraft($user);
        $this->actingAs($user)->post('/calon/pendaftaran/submit', ['pernyataan' => 1]);

        $pendaftaran = $pendaftaran->fresh();
        $pendaftaran->update(['status' => StatusPendaftaran::SELECTED]);

        Pengumuman::create([
            'ppdb_id' => $pendaftaran->ppdb_id,
            'pendaftaran_id' => $pendaftaran->id,
            'tanggal_pengumuman' => now(),
            'hasil' => Pengumuman::HASIL_DITERIMA,
            'keterangan' => 'Silakan lakukan daftar ulang.',
            'dipublikasikan' => true,
        ]);

        $this->actingAs($user)
            ->get('/calon/hasil')
            ->assertOk()
            ->assertSee('Selamat, Anda Diterima')
            ->assertSee('Silakan lakukan daftar ulang.');

        $this->actingAs($user)
            ->get('/calon/status')
            ->assertOk()
            ->assertSee('Selamat, Anda Diterima!');

        $this->actingAs($user)
            ->get('/calon/daftar-ulang')
            ->assertOk();
    }

    private function createPendaftar(): User
    {
        return User::factory()->create();
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

        $this->uploadRequiredDokumen($user);

        return $this->pendaftaranFor($user);
    }

    private function uploadRequiredDokumen(User $user): void
    {
        foreach (JenisDokumen::where('wajib', true)->orderBy('id')->get() as $jenis) {
            $file = str_contains(strtolower($jenis->format_file), 'jpg')
                ? UploadedFile::fake()->image('dokumen-'.$jenis->id.'.jpg')
                : UploadedFile::fake()->create('dokumen-'.$jenis->id.'.pdf', 100, 'application/pdf');

            $this->actingAs($user)
                ->post('/calon/pendaftaran/dokumen', [
                    'jenis_dokumen_id' => $jenis->id,
                    'file' => $file,
                ])
                ->assertRedirect();
        }
    }

    private function pendaftaranFor(User $user): Pendaftaran
    {
        return Pendaftaran::whereHas('calonSiswa', fn ($query) => $query->where('user_id', $user->id))
            ->firstOrFail();
    }

    private function pendaftaranCount(User $user): int
    {
        return Pendaftaran::whereHas('calonSiswa', fn ($query) => $query->where('user_id', $user->id))
            ->count();
    }
}