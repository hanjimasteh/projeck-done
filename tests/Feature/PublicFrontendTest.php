<?php

namespace Tests\Feature;

use App\Models\Pendaftaran;
use App\Models\Pengumuman;
use App\Models\Ppdb;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class PublicFrontendTest extends TestCase
{
    use RefreshDatabase;

    private const PUBLIC_ROUTES = [
        '/',
        '/informasi',
        '/jadwal',
        '/persyaratan',
        '/alur',
        '/pengumuman',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_all_public_pages_can_be_opened_without_login(): void
    {
        foreach (self::PUBLIC_ROUTES as $route) {
            $this->get($route)->assertOk();
        }
    }

    public function test_pages_display_data_from_the_active_ppdb_period(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Penerimaan Tahun Ajaran 2027/2028')
            ->assertSee('Reguler')
            ->assertSee('PPLG');

        $this->get('/informasi')
            ->assertOk()
            ->assertSee('Pengembangan Perangkat Lunak dan Gim')
            ->assertSee('72');

        $this->get('/jadwal')
            ->assertOk()
            ->assertSee('Pendaftaran')
            ->assertSee('Daftar Ulang');

        $this->get('/persyaratan')
            ->assertOk()
            ->assertSee('Kartu Keluarga')
            ->assertSee('Wajib')
            ->assertSee('2 MB');

        $this->get('/alur')
            ->assertOk()
            ->assertSee('Upload Berkas')
            ->assertSee('Daftar Ulang');

        $this->get('/pengumuman')
            ->assertOk()
            ->assertSee('Cek Hasil Seleksi')
            ->assertSee('Hasil seleksi belum diumumkan');
    }

    public function test_pages_show_closed_message_when_no_ppdb_is_active(): void
    {
        Ppdb::query()->update(['status' => 'selesai']);

        foreach (self::PUBLIC_ROUTES as $route) {
            $this->get($route)
                ->assertOk()
                ->assertSee('Pendaftaran belum dibuka');
        }
    }

    public function test_schedule_marks_the_current_stage(): void
    {
        Carbon::setTestNow('2027-03-15 09:00:00');

        try {
            $this->get('/jadwal')
                ->assertOk()
                ->assertSee('Sedang Berlangsung');

            $this->get('/')
                ->assertOk()
                ->assertSee('Tahap')
                ->assertSee('Pendaftaran');
        } finally {
            Carbon::setTestNow();
        }
    }

    public function test_result_check_shows_not_announced_message_when_no_pengumuman_is_published(): void
    {
        $this->followingRedirects()
            ->post('/pengumuman/cek', [
                'nomor_pendaftaran' => 'PPDB-2027-0001',
                'tanggal_lahir' => '2009-01-01',
            ])
            ->assertOk()
            ->assertSee('Hasil seleksi belum diumumkan');
    }

    public function test_result_check_displays_published_result(): void
    {
        $pendaftaran = Pendaftaran::firstOrFail();

        Pengumuman::create([
            'ppdb_id' => $pendaftaran->ppdb_id,
            'pendaftaran_id' => $pendaftaran->id,
            'tanggal_pengumuman' => now(),
            'hasil' => Pengumuman::HASIL_DITERIMA,
            'keterangan' => 'Silakan lakukan daftar ulang sesuai jadwal.',
            'dipublikasikan' => true,
        ]);

        $this->followingRedirects()
            ->post('/pengumuman/cek', [
                'nomor_pendaftaran' => 'PPDB-2027-0001',
                'tanggal_lahir' => '2009-01-01',
            ])
            ->assertOk()
            ->assertSee('Selamat, Anda dinyatakan diterima!')
            ->assertSee('Calon Pendaftar Demo')
            ->assertSee('Silakan lakukan daftar ulang sesuai jadwal.');
    }
}
