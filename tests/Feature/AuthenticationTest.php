<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_registration_creates_a_pendaftar_and_an_empty_calon_siswa(): void
    {
        $response = $this->post('/register', [
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@example.test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('login'))->assertSessionHas('success');

        $user = User::where('username', 'budisantoso')->firstOrFail();

        $this->assertSame(Role::PENDAFTAR, $user->role);
        $this->assertSame('aktif', $user->status);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertNotNull($user->fresh()->calonSiswa);
        $this->assertNull($user->fresh()->calonSiswa->nama_lengkap);
    }

    public function test_login_accepts_email_or_username_and_redirects_by_role(): void
    {
        $admin = $this->user('admin');
        $pendaftar = $this->user('pendaftar');

        $this->post('/login', [
            'login' => 'admin@ppdb.test',
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->post('/logout')->assertRedirect(route('home'));
        $this->assertGuest();

        $this->post('/login', [
            'login' => 'pendaftar',
            'password' => 'password',
        ])->assertRedirect(route('calon.dashboard'));

        $this->assertAuthenticatedAs($pendaftar);
    }

    public function test_inactive_account_cannot_login(): void
    {
        $pendaftar = $this->user('pendaftar');
        $pendaftar->update(['status' => 'nonaktif']);

        $this->from('/login')
            ->post('/login', [
                'login' => 'pendaftar',
                'password' => 'password',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'login' => 'Akun Anda tidak aktif. Silakan hubungi panitia PPDB.',
            ]);

        $this->assertGuest();
    }

    public function test_wrong_credentials_return_a_clear_error(): void
    {
        $this->from('/login')
            ->post('/login', [
                'login' => 'pendaftar',
                'password' => 'password-salah',
            ])
            ->assertRedirect('/login')
            ->assertSessionHasErrors([
                'login' => 'Email/username atau password yang Anda masukkan salah.',
            ]);

        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login_for_protected_areas(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
        $this->get('/calon/dashboard')->assertRedirect(route('login'));
        $this->get('/calon/profil')->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_pendaftar_cannot_access_the_admin_area(): void
    {
        $pendaftar = $this->user('pendaftar');

        $this->actingAs($pendaftar)->get('/admin/dashboard')->assertForbidden();
        $this->actingAs($pendaftar)->get('/admin/verifikasi')->assertForbidden();
    }

    public function test_admin_cannot_access_the_pendaftar_area(): void
    {
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/calon/dashboard')->assertForbidden();
        $this->actingAs($admin)->get('/calon/profil')->assertForbidden();
    }

    public function test_each_role_can_open_its_own_dashboard(): void
    {
        $admin = $this->user('admin');
        $pendaftar = $this->user('pendaftar');

        $this->actingAs($admin)
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Selamat datang, '.$admin->name)
            ->assertSee('Keluar');

        $this->actingAs($pendaftar)
            ->get('/calon/dashboard')
            ->assertOk()
            ->assertSee('Selamat datang, '.$pendaftar->name)
            ->assertSee('Keluar');
    }

    public function test_logout_works_for_both_roles(): void
    {
        foreach ([$this->user('admin'), $this->user('pendaftar')] as $user) {
            $this->actingAs($user)
                ->post('/logout')
                ->assertRedirect(route('home'));

            $this->assertGuest();
        }
    }

    public function test_pendaftar_can_update_profile_and_password(): void
    {
        $pendaftar = $this->user('pendaftar');

        $this->actingAs($pendaftar)
            ->get('/calon/profil')
            ->assertOk()
            ->assertSee('Ubah Profil');

        $this->actingAs($pendaftar)
            ->put('/calon/profil', [
                'name' => 'Nama Baru Pendaftar',
                'email' => 'nama.baru@example.test',
                'current_password' => 'password',
                'password' => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ])
            ->assertRedirect(route('calon.profil.edit'))
            ->assertSessionHas('success');

        $pendaftar->refresh();

        $this->assertSame('Nama Baru Pendaftar', $pendaftar->name);
        $this->assertSame('nama.baru@example.test', $pendaftar->email);
        $this->assertTrue(Hash::check('passwordbaru123', $pendaftar->password));
        $this->assertDatabaseHas('calon_siswa', [
            'user_id' => $pendaftar->id,
            'nama_lengkap' => 'Nama Baru Pendaftar',
        ]);
    }

    public function test_profile_rejects_an_incorrect_current_password(): void
    {
        $pendaftar = $this->user('pendaftar');

        $this->actingAs($pendaftar)
            ->from(route('calon.profil.edit'))
            ->put('/calon/profil', [
                'name' => $pendaftar->name,
                'email' => $pendaftar->email,
                'current_password' => 'password-salah',
                'password' => 'passwordbaru123',
                'password_confirmation' => 'passwordbaru123',
            ])
            ->assertRedirect(route('calon.profil.edit'))
            ->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $pendaftar->fresh()->password));
    }

    public function test_login_is_throttled_after_five_failed_attempts(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from('/login')->post('/login', [
                'login' => 'admin@ppdb.test',
                'password' => 'password-salah',
            ]);
        }

        $response = $this->from('/login')->post('/login', [
            'login' => 'admin@ppdb.test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/login')->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->assertStringContainsString(
            'Terlalu banyak percobaan login',
            session('errors')->first('login')
        );
    }

    public function test_preview_routes_are_removed(): void
    {
        $this->get('/_preview/backend')->assertNotFound();
        $this->get('/_preview/calon')->assertNotFound();
    }

    private function user(string $username): User
    {
        return User::where('username', $username)->firstOrFail();
    }
}
