<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Enums\StatusDokumen;
use App\Enums\StatusPendaftaran;
use App\Models\CalonSiswa;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\OrangTua;
use App\Models\Pendaftaran;
use App\Models\Ppdb;
use App\Models\Program;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_seeders_create_expected_data_and_are_idempotent(): void
    {
        $this->seed();

        $this->assertDatabaseCount('ppdb', 2);
        $this->assertDatabaseCount('program', 4);
        $this->assertDatabaseCount('jadwal_ppdb', 5);
        $this->assertDatabaseCount('jenis_dokumen', 6);
        $this->assertDatabaseCount('persyaratan', 6);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('calon_siswa', 1);
        $this->assertDatabaseCount('pendaftaran', 1);

        $this->assertSame(1, Ppdb::aktif()->count());
        $this->assertSame('2027/2028', Ppdb::aktif()->first()->tahun_ajaran);
    }

    public function test_demo_registration_relations(): void
    {
        $pendaftaran = Pendaftaran::with([
            'ppdb',
            'calonSiswa.user',
            'programPilihan1',
            'programPilihan2',
            'dokumen',
            'seleksi',
            'pengumuman',
            'daftarUlang',
        ])->firstOrFail();

        $this->assertSame('PPDB-2027-0001', $pendaftaran->nomor_pendaftaran);
        $this->assertSame(StatusPendaftaran::SUBMITTED, $pendaftaran->status);
        $this->assertSame('Calon Pendaftar Demo', $pendaftaran->calonSiswa->nama_lengkap);
        $this->assertSame('pendaftar', $pendaftaran->calonSiswa->user->username);
        $this->assertSame('PPLG', $pendaftaran->programPilihan1->kode);
        $this->assertSame('TJKT', $pendaftaran->programPilihan2->kode);
        $this->assertSame('2027/2028', $pendaftaran->ppdb->tahun_ajaran);
        $this->assertCount(0, $pendaftaran->dokumen);
        $this->assertNull($pendaftaran->seleksi);
        $this->assertNull($pendaftaran->pengumuman);
        $this->assertNull($pendaftaran->daftarUlang);
    }

    public function test_document_parent_and_reverse_relations(): void
    {
        $pendaftaran = Pendaftaran::firstOrFail();
        $jenisDokumen = JenisDokumen::firstOrFail();

        $dokumen = Dokumen::create([
            'pendaftaran_id' => $pendaftaran->id,
            'jenis_dokumen_id' => $jenisDokumen->id,
            'file' => 'dokumen/demo/kartu-keluarga.pdf',
            'status' => StatusDokumen::PENDING,
        ]);

        $this->assertSame(StatusDokumen::PENDING, $dokumen->status);
        $this->assertTrue($pendaftaran->fresh()->dokumen->contains($dokumen));
        $this->assertTrue($jenisDokumen->fresh()->dokumen->contains($dokumen));
        $this->assertTrue($dokumen->pendaftaran->is($pendaftaran));
        $this->assertTrue($dokumen->jenisDokumen->is($jenisDokumen));
    }

    public function test_calon_siswa_parent_relations(): void
    {
        $siswa = CalonSiswa::whereHas('user', fn ($query) => $query->where('username', 'pendaftar'))
            ->firstOrFail();

        $orangTua = OrangTua::create([
            'calon_siswa_id' => $siswa->id,
            'hubungan' => 'ayah',
            'nama' => 'Ayah Demo',
            'pekerjaan' => 'Karyawan',
            'no_hp' => '081200000001',
            'alamat' => 'Jl. Contoh No. 1, Bandung',
        ]);

        $this->assertSame('Calon Pendaftar Demo', $siswa->user->nama_lengkap ?? $siswa->user->name);
        $this->assertTrue($siswa->fresh()->orangTua->contains($orangTua));
        $this->assertTrue($orangTua->calonSiswa->is($siswa));
        $this->assertSame(1, $siswa->pendaftaran->count());
        $this->assertTrue($siswa->pendaftaran->first()->programPilihan1->is(Program::where('kode', 'PPLG')->first()));
    }

    public function test_role_helper_and_enum_labels(): void
    {
        $admin = User::where('username', 'admin')->firstOrFail();
        $pendaftar = User::where('username', 'pendaftar')->firstOrFail();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($pendaftar->isAdmin());
        $this->assertSame(Role::ADMIN, $admin->role);
        $this->assertSame(Role::PENDAFTAR, $pendaftar->role);

        foreach (Role::cases() as $role) {
            $this->assertNotSame('', $role->label());
            $this->assertNotSame('', $role->badgeClass());
        }

        foreach (StatusPendaftaran::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertNotSame('', $status->badgeClass());
        }

        foreach (StatusDokumen::cases() as $status) {
            $this->assertNotSame('', $status->label());
            $this->assertNotSame('', $status->badgeClass());
        }
    }
}