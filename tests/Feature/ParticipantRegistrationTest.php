<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ParticipantRegistrationTest extends TestCase
{
    use RefreshDatabase;

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

    private function createCompetition(User $organizer, array $overrides = []): Competition
    {
        return Competition::create(array_merge([
            'organizer_id' => $organizer->id,
            'judul' => 'ITB Hackathon 2026',
            'kategori' => 'Teknologi',
            'jenjang' => 'Mahasiswa',
            'mode_pelaksanaan' => 'Hybrid',
            'biaya_pendaftaran' => 150000,
            'kuota' => 50,
            'kuota_terisi' => 0,
            'deadline_pendaftaran' => now()->addDays(14)->toDateString(),
            'deskripsi' => 'Kompetisi inovasi teknologi nasional.',
            'status' => 'Aktif',
        ], $overrides));
    }

    public function test_custom_helpers_format_values_correctly(): void
    {
        $this->assertSame('Rp 50.000', formatRupiah(50000));
        $this->assertSame('Gratis', formatRupiah(0));
        $this->assertSame('20 November 2026', formatTanggalIndo('2026-11-20'));
        $this->assertSame('-', formatTanggalIndo(null));
        $this->assertStringContainsString('Menunggu Verifikasi', statusBadge('menunggu_verifikasi'));
        $this->assertStringContainsString('Lolos Seleksi', statusBadge('diterima'));
        $this->assertStringContainsString('Berkas Ditolak', statusBadge('ditolak'));
        $this->assertStringContainsString('Lunas', statusBadge('lunas'));
        $this->assertStringContainsString('Belum Bayar', statusBadge('menunggu_pembayaran'));
    }

    public function test_guest_and_organizer_cannot_access_participant_registration_routes(): void
    {
        $organizer = $this->createOrganizer();
        $competition = $this->createCompetition($organizer);

        $this->get(route('peserta.daftar.create', $competition->id))
            ->assertRedirect('/login');

        $this->actingAs($organizer)
            ->get(route('peserta.pendaftaran.index'))
            ->assertForbidden();
    }

    public function test_peserta_can_view_registration_form_for_open_competition(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);

        $response = $this->actingAs($peserta)
            ->get(route('peserta.daftar.create', $competition->id));

        $response->assertStatus(200);
        $response->assertSee('ITB Hackathon 2026');
        $response->assertSee('Rp 150.000');
    }

    public function test_closed_or_full_competition_blocks_registration_form(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $fullCompetition = $this->createCompetition($organizer, [
            'kuota' => 10,
            'kuota_terisi' => 10,
        ]);

        $response = $this->actingAs($peserta)
            ->get(route('peserta.daftar.create', $fullCompetition->id));

        $response->assertRedirect(route('peserta.pendaftaran.index'));
        $response->assertSessionHas('error');
    }

    public function test_registration_validation_rejects_invalid_input_and_files(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);

        $response = $this->actingAs($peserta)
            ->from(route('peserta.daftar.create', $competition->id))
            ->post(route('peserta.daftar.store'), [
                'competition_id' => $competition->id,
                'nama_tim' => 'AB',
                'nama_ketua' => '',
                'email_ketua' => 'bukan-email',
                'no_hp_ketua' => '123',
                'nama_anggota_1' => 'Budi Santoso',
                'ktm_ketua' => UploadedFile::fake()->create('script.exe', 100),
            ]);

        $response->assertRedirect(route('peserta.daftar.create', $competition->id));
        $response->assertSessionHasErrors([
            'nama_tim',
            'nama_ketua',
            'email_ketua',
            'no_hp_ketua',
            'email_anggota_1',
            'no_hp_anggota_1',
            'ktm_ketua',
        ]);
    }

    public function test_peserta_can_submit_team_registration_upload_documents_and_pay_invoice(): void
    {
        Storage::fake('public');

        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $otherPeserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);

        $response = $this->actingAs($peserta)->post(route('peserta.daftar.store'), [
            'competition_id' => $competition->id,
            'nama_tim' => 'Tim TechnoNova',
            'nama_ketua' => 'Ahmad Shafy',
            'email_ketua' => 'shafy@example.com',
            'no_hp_ketua' => '081234567890',
            'nama_anggota_1' => 'Syauqi Hafid',
            'email_anggota_1' => 'syauqi@example.com',
            'no_hp_anggota_1' => '081234567891',
            'ktm_ketua' => UploadedFile::fake()->create('ktm_ketua.pdf', 200, 'application/pdf'),
            'proposal' => UploadedFile::fake()->create('proposal.pdf', 500, 'application/pdf'),
        ]);

        $registration = Registration::first();
        $this->assertNotNull($registration);
        $this->assertSame('Tim TechnoNova', $registration->nama_tim);
        $this->assertSame('menunggu_verifikasi', $registration->status_verifikasi);
        $this->assertCount(2, $registration->members);
        $this->assertCount(2, $registration->documents);

        foreach ($registration->documents as $doc) {
            Storage::disk('public')->assertExists($doc->file_path);
        }

        $payment = Payment::where('registration_id', $registration->id)->first();
        $this->assertNotNull($payment);
        $this->assertSame(150000, $payment->nominal);
        $this->assertSame('menunggu_pembayaran', $payment->status_pembayaran);

        $this->assertSame(1, $competition->fresh()->kuota_terisi);

        $response->assertRedirect(route('peserta.invoice.show', $payment->id));

        // Halaman Pendaftaran Saya menampilkan tim dan status
        $this->actingAs($peserta)
            ->get(route('peserta.pendaftaran.index'))
            ->assertStatus(200)
            ->assertSee('Tim TechnoNova')
            ->assertSee('Menunggu Verifikasi')
            ->assertSee($payment->kode_invoice);

        // Peserta lain dilarang melihat invoice peserta ini
        $this->actingAs($otherPeserta)
            ->get(route('peserta.invoice.show', $payment->id))
            ->assertStatus(403);

        // Simulasi pembayaran oleh pemilik invoice
        $payResponse = $this->actingAs($peserta)
            ->post(route('peserta.invoice.bayar', $payment->id), [
                'metode_pembayaran' => 'QRIS',
            ]);

        $payResponse->assertRedirect(route('peserta.invoice.show', $payment->id));
        $payResponse->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame('lunas', $payment->status_pembayaran);
        $this->assertSame('QRIS', $payment->metode_pembayaran);
        $this->assertNotNull($payment->waktu_bayar);
    }

    public function test_free_competition_automatically_marks_payment_as_lunas(): void
    {
        Storage::fake('public');

        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $freeCompetition = $this->createCompetition($organizer, [
            'judul' => 'Olimpiade Gratis 2026',
            'biaya_pendaftaran' => 0,
        ]);

        $this->actingAs($peserta)->post(route('peserta.daftar.store'), [
            'competition_id' => $freeCompetition->id,
            'nama_tim' => 'Tim Gratis Jaya',
            'nama_ketua' => 'Ketua Gratis',
            'email_ketua' => 'gratis@example.com',
            'no_hp_ketua' => '081234567890',
            'ktm_ketua' => UploadedFile::fake()->image('ktm.png'),
        ]);

        $payment = Payment::first();
        $this->assertNotNull($payment);
        $this->assertSame('lunas', $payment->status_pembayaran);
        $this->assertNotNull($payment->waktu_bayar);
    }
}
