<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Competition;
use App\Models\Notification;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacAndMasterCategoryTest extends TestCase
{
    use RefreshDatabase;

    /*
    |--------------------------------------------------------------------------
    | 1. RBAC
    |--------------------------------------------------------------------------
    */

    public function test_guest_is_redirected_to_login_from_organizer_area(): void
    {
        $this->get('/penyelenggara/lomba')
            ->assertRedirect('/login');

        $this->get('/penyelenggara/kategori')
            ->assertRedirect('/login');
    }

    public function test_peserta_cannot_access_organizer_area(): void
    {
        $peserta = $this->createPeserta();

        $urls = [
            '/penyelenggara/lomba',
            '/penyelenggara/kategori',
            '/penyelenggara/validasi',
            '/penyelenggara/laporan',
        ];

        foreach ($urls as $url) {
            $this->actingAs($peserta)
                ->get($url)
                ->assertForbidden();
        }
    }

    public function test_penyelenggara_cannot_access_peserta_area(): void
    {
        $organizer = $this->createOrganizer();
        $competition = $this->createCompetition($organizer);

        $this->actingAs($organizer)
            ->get('/peserta/pendaftaran')
            ->assertForbidden();

        $this->actingAs($organizer)
            ->get('/peserta/daftar/'.$competition->id)
            ->assertForbidden();
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Master Kategori (M1) & Lomba (M3)
    |--------------------------------------------------------------------------
    */

    public function test_penyelenggara_can_create_category(): void
    {
        $organizer = $this->createOrganizer();

        $this->actingAs($organizer)
            ->post(route('penyelenggara.kategori.store'), [
                'nama_kategori' => 'Robotika',
                'slug' => 'robotika',
                'deskripsi' => 'Kompetisi robotika dan otomasi.',
                'ikon' => 'bi-robot',
            ])
            ->assertRedirect(route('penyelenggara.kategori.index'))
            ->assertSessionHas('success', 'Kategori berhasil ditambahkan!');

        $this->assertDatabaseHas('categories', [
            'nama_kategori' => 'Robotika',
            'slug' => 'robotika',
            'deskripsi' => 'Kompetisi robotika dan otomasi.',
            'ikon' => 'bi-robot',
        ]);
    }

    public function test_penyelenggara_can_update_category(): void
    {
        $organizer = $this->createOrganizer();
        $category = $this->createCategory([
            'nama_kategori' => 'Teknologi',
            'slug' => 'teknologi',
            'ikon' => 'bi-cpu',
        ]);

        $this->actingAs($organizer)
            ->put(route('penyelenggara.kategori.update', $category->id), [
                'nama_kategori' => 'Teknologi Informasi',
                'slug' => 'teknologi-informasi',
                'deskripsi' => 'Kompetisi bidang TI.',
                'ikon' => 'bi-laptop',
            ])
            ->assertRedirect(route('penyelenggara.kategori.index'))
            ->assertSessionHas('success', 'Kategori berhasil diperbarui!');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'nama_kategori' => 'Teknologi Informasi',
            'slug' => 'teknologi-informasi',
            'ikon' => 'bi-laptop',
        ]);
    }

    public function test_penyelenggara_can_search_categories_by_name_and_description(): void
    {
        $organizer = $this->createOrganizer();

        $this->createCategory([
            'nama_kategori' => 'Teknologi',
            'slug' => 'teknologi',
            'deskripsi' => 'Kompetisi pemrograman dan kecerdasan buatan.',
        ]);
        $this->createCategory([
            'nama_kategori' => 'Desain',
            'slug' => 'desain',
            'deskripsi' => 'Kompetisi antarmuka dan grafis.',
        ]);

        $this->actingAs($organizer)
            ->get(route('penyelenggara.kategori.index', ['q' => 'kecerdasan buatan']))
            ->assertOk()
            ->assertSee('Teknologi')
            ->assertDontSee('Desain')
            ->assertViewHas('search', 'kecerdasan buatan');
    }

    public function test_category_index_is_paginated_ten_per_page(): void
    {
        $organizer = $this->createOrganizer();

        foreach (range(1, 12) as $i) {
            $this->createCategory([
                'nama_kategori' => sprintf('Kategori %02d', $i),
                'slug' => sprintf('kategori-%02d', $i),
            ]);
        }

        $firstPage = $this->actingAs($organizer)
            ->get(route('penyelenggara.kategori.index'));

        $firstPage->assertOk()
            ->assertViewHas('categories', fn ($categories) => $categories->total() === 12
                && $categories->perPage() === 10
                && $categories->count() === 10)
            ->assertSee('Kategori 01')
            ->assertDontSee('Kategori 12');

        $this->actingAs($organizer)
            ->get(route('penyelenggara.kategori.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Kategori 11')
            ->assertSee('Kategori 12')
            ->assertDontSee('Kategori 01');
    }

    public function test_category_used_by_competition_cannot_be_deleted(): void
    {
        $organizer = $this->createOrganizer();
        $category = $this->createCategory([
            'nama_kategori' => 'Teknologi',
            'slug' => 'teknologi',
        ]);

        $this->createCompetition($organizer, ['kategori' => 'Teknologi']);

        $this->actingAs($organizer)
            ->from(route('penyelenggara.kategori.index'))
            ->delete(route('penyelenggara.kategori.destroy', $category->id))
            ->assertRedirect(route('penyelenggara.kategori.index'))
            ->assertSessionHas('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh data kompetisi.');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_competition_with_registration_cannot_be_deleted(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);

        $this->createRegistration($competition, $peserta);

        $this->actingAs($organizer)
            ->from(route('penyelenggara.lomba.index'))
            ->delete(route('penyelenggara.lomba.destroy', $competition->id))
            ->assertRedirect(route('penyelenggara.lomba.index'))
            ->assertSessionHas('error', 'Kompetisi tidak dapat dihapus karena sudah memiliki data pendaftaran peserta.');

        $this->assertDatabaseHas('competitions', ['id' => $competition->id]);
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Verifikasi (T2)
    |--------------------------------------------------------------------------
    */

    public function test_organizer_rejection_updates_audit_quota_and_notification(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer, ['kuota_terisi' => 1]);
        $registration = $this->createRegistration($competition, $peserta);

        $this->actingAs($organizer)
            ->patch(route('penyelenggara.validasi.update', $registration->id), [
                'status_verifikasi' => 'ditolak',
                'catatan_kurasi' => 'Proposal belum lengkap.',
            ])
            ->assertRedirect(route('penyelenggara.validasi.index'))
            ->assertSessionHas('success', 'Status verifikasi berhasil diperbarui!');

        $registration->refresh();

        $this->assertSame('ditolak', $registration->status_verifikasi);
        $this->assertSame($organizer->id, $registration->verified_by);
        $this->assertNotNull($registration->verified_at);
        $this->assertSame($organizer->id, $registration->verifier?->id);
        $this->assertSame(0, $competition->fresh()->kuota_terisi);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $peserta->id,
            'judul' => 'Pendaftaran Tim Ditolak',
            'tipe' => 'verifikasi',
        ]);

        $notification = Notification::where('user_id', $peserta->id)->firstOrFail();
        $this->assertStringContainsString('ditolak', $notification->pesan);
        $this->assertStringContainsString('Proposal belum lengkap.', $notification->pesan);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    private function createOrganizer(): User
    {
        return User::factory()->create([
            'role' => 'penyelenggara',
        ]);
    }

    private function createPeserta(): User
    {
        return User::factory()->create([
            'role' => 'peserta',
        ]);
    }

    private function createCategory(array $overrides = []): Category
    {
        return Category::create(array_merge([
            'nama_kategori' => 'Kategori '.fake()->unique()->numerify('###'),
            'slug' => 'kategori-'.fake()->unique()->numerify('###'),
            'deskripsi' => 'Deskripsi kategori uji.',
            'ikon' => 'bi-tag',
        ], $overrides));
    }

    private function createCompetition(User $organizer, array $overrides = []): Competition
    {
        return Competition::create(array_merge([
            'organizer_id' => $organizer->id,
            'judul' => 'Lomba Uji '.fake()->unique()->numerify('###'),
            'kategori' => 'Teknologi',
            'jenjang' => 'Mahasiswa',
            'mode_pelaksanaan' => 'Online',
            'biaya_pendaftaran' => 50000,
            'kuota' => 50,
            'kuota_terisi' => 0,
            'deadline_pendaftaran' => now()->addDays(10)->toDateString(),
            'deskripsi' => 'Deskripsi lomba uji.',
            'status' => 'Aktif',
        ], $overrides));
    }

    private function createRegistration(Competition $competition, User $peserta, array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'competition_id' => $competition->id,
            'peserta_id' => $peserta->id,
            'nama_tim' => 'Tim Uji '.fake()->unique()->numerify('###'),
            'status_verifikasi' => 'menunggu_verifikasi',
        ], $overrides));
    }
}
