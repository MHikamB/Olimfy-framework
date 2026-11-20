<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SubmissionAndReportTest extends TestCase
{
    use RefreshDatabase;

    private function createOrganizer(string $name = 'Panitia ITB'): User
    {
        return User::factory()->create([
            'name' => $name,
            'role' => 'penyelenggara',
        ]);
    }

    private function createPeserta(string $name = 'Peserta Tim'): User
    {
        return User::factory()->create([
            'name' => $name,
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
            'kuota' => 100,
            'kuota_terisi' => 1,
            'deadline_pendaftaran' => now()->addDays(14)->toDateString(),
            'deskripsi' => 'Kompetisi pengembangan aplikasi AI.',
            'status' => 'Aktif',
        ], $overrides));
    }

    private function createRegistrationWithPayment(
        Competition $competition,
        User $peserta,
        string $statusVerifikasi = 'diterima',
        string $statusPembayaran = 'lunas',
        string $namaTim = 'Tim TechnoNova'
    ): Registration {
        $registration = Registration::create([
            'competition_id' => $competition->id,
            'peserta_id' => $peserta->id,
            'nama_tim' => $namaTim,
            'status_verifikasi' => $statusVerifikasi,
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'paid_by' => $statusPembayaran === 'lunas' ? $peserta->id : null,
            'kode_invoice' => sprintf('INV-TEST-%04d', $registration->id),
            'nominal' => $competition->biaya_pendaftaran,
            'metode_pembayaran' => 'QRIS',
            'status_pembayaran' => $statusPembayaran,
            'waktu_bayar' => $statusPembayaran === 'lunas' ? now() : null,
        ]);

        return $registration;
    }

    public function test_t3_payment_simulation_records_paid_by_and_creates_notification(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);
        $registration = $this->createRegistrationWithPayment($competition, $peserta, 'menunggu_verifikasi', 'menunggu_pembayaran');
        $payment = $registration->payment;

        $response = $this->actingAs($peserta)
            ->post(route('peserta.invoice.bayar', $payment->id), [
                'metode_pembayaran' => 'Virtual Account BCA',
            ]);

        $response->assertRedirect(route('peserta.invoice.show', $payment->id));
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertSame('lunas', $payment->status_pembayaran);
        $this->assertSame($peserta->id, $payment->paid_by);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $peserta->id,
            'tipe' => 'pembayaran',
            'is_read' => false,
        ]);
    }

    public function test_t4_ineligible_or_unauthorized_peserta_cannot_submit_work(): void
    {
        $organizer = $this->createOrganizer();
        $peserta = $this->createPeserta();
        $otherPeserta = $this->createPeserta('Peserta Lain');
        $competition = $this->createCompetition($organizer);

        $pendingReg = $this->createRegistrationWithPayment($competition, $peserta, 'menunggu_verifikasi', 'lunas', 'Tim Pending');
        $unpaidReg = $this->createRegistrationWithPayment($competition, $peserta, 'diterima', 'menunggu_pembayaran', 'Tim Belum Bayar');

        $this->actingAs($peserta)
            ->get(route('peserta.submission.create', $pendingReg->id))
            ->assertRedirect(route('peserta.submission.index'))
            ->assertSessionHas('error');

        $this->actingAs($peserta)
            ->post(route('peserta.submission.store', $unpaidReg->id), [
                'judul_karya' => 'Aplikasi Keren',
                'deskripsi_karya' => 'Deskripsi karya minimal dua puluh karakter panjangnya.',
            ])
            ->assertRedirect(route('peserta.submission.index'))
            ->assertSessionHas('error');

        $this->actingAs($otherPeserta)
            ->get(route('peserta.submission.create', $pendingReg->id))
            ->assertStatus(403);
    }

    public function test_t4_two_role_submission_and_grading_flow(): void
    {
        Storage::fake('public');

        $organizer = $this->createOrganizer();
        $otherOrganizer = $this->createOrganizer('Panitia Lain');
        $peserta = $this->createPeserta();
        $competition = $this->createCompetition($organizer);
        $registration = $this->createRegistrationWithPayment($competition, $peserta, 'diterima', 'lunas');

        // 1. Peserta mengumpulkan karya
        $submitResponse = $this->actingAs($peserta)
            ->post(route('peserta.submission.store', $registration->id), [
                'judul_karya' => 'SmartTraffic AI',
                'deskripsi_karya' => 'Solusi pengurai kemacetan berbasis computer vision real-time.',
                'link_karya' => 'https://github.com/example/smarttraffic',
                'file_karya' => UploadedFile::fake()->create('karya.pdf', 300, 'application/pdf'),
            ]);

        $submitResponse->assertRedirect(route('peserta.submission.index'));
        $submitResponse->assertSessionHas('success');

        $submission = Submission::first();
        $this->assertNotNull($submission);
        $this->assertSame('dikirim', $submission->status);
        $this->assertSame($peserta->id, $submission->submitted_by);
        $this->assertNotNull($submission->submitted_at);
        Storage::disk('public')->assertExists($submission->file_karya);

        // 2. Penyelenggara lain tidak dapat menilai karya ini
        $this->actingAs($otherOrganizer)
            ->patch(route('penyelenggara.submission.grade', $submission->id), [
                'nilai' => 80,
                'status' => 'dinilai',
            ])
            ->assertStatus(404);

        // 3. Penyelenggara pemilik lomba menilai karya
        $gradeResponse = $this->actingAs($organizer)
            ->patch(route('penyelenggara.submission.grade', $submission->id), [
                'nilai' => 92,
                'status' => 'finalis',
                'catatan_juri' => 'Arsitektur sangat solid dan demo berjalan baik.',
            ]);

        $gradeResponse->assertRedirect(route('penyelenggara.submission.index'));
        $gradeResponse->assertSessionHas('success');

        $submission->refresh();
        $this->assertSame(92, $submission->nilai);
        $this->assertSame('finalis', $submission->status);
        $this->assertSame($organizer->id, $submission->graded_by);
        $this->assertNotNull($submission->graded_at);

        // 4. Notifikasi hasil penilaian terkirim ke peserta
        $this->assertTrue(
            Notification::where('user_id', $peserta->id)
                ->where('tipe', 'info')
                ->where('judul', 'like', '%SmartTraffic AI%')
                ->exists()
        );

        // 5. Karya yang sudah dinilai terkunci dari pengubahan ulang peserta
        $this->actingAs($peserta)
            ->get(route('peserta.submission.create', $registration->id))
            ->assertRedirect(route('peserta.submission.index'))
            ->assertSessionHas('error');
    }

    public function test_query_builder_report_displays_accurate_aggregates_and_filters(): void
    {
        $organizer = $this->createOrganizer();
        $peserta1 = $this->createPeserta('Peserta 1');
        $peserta2 = $this->createPeserta('Peserta 2');

        $techComp = $this->createCompetition($organizer, [
            'judul' => 'Hackathon AI',
            'kategori' => 'Teknologi',
            'biaya_pendaftaran' => 150000,
        ]);

        $designComp = $this->createCompetition($organizer, [
            'judul' => 'UI/UX Challenge',
            'kategori' => 'Desain',
            'biaya_pendaftaran' => 50000,
        ]);

        // 2 tim di Hackathon AI (1 lunas + diterima + dinilai, 1 belum bayar + ditolak)
        $reg1 = $this->createRegistrationWithPayment($techComp, $peserta1, 'diterima', 'lunas', 'Tim Alpha');
        Submission::create([
            'registration_id' => $reg1->id,
            'competition_id' => $techComp->id,
            'submitted_by' => $peserta1->id,
            'judul_karya' => 'Karya Alpha',
            'deskripsi_karya' => 'Deskripsi karya Alpha lebih dari dua puluh karakter.',
            'status' => 'dinilai',
            'nilai' => 88,
            'graded_by' => $organizer->id,
            'submitted_at' => now(),
            'graded_at' => now(),
        ]);

        $this->createRegistrationWithPayment($techComp, $peserta2, 'ditolak', 'menunggu_pembayaran', 'Tim Beta');

        // 1 tim di UI/UX Challenge (lunas + diterima)
        $this->createRegistrationWithPayment($designComp, $peserta1, 'diterima', 'lunas', 'Tim Gamma');

        // Peserta tidak boleh buka laporan penyelenggara
        $this->actingAs($peserta1)
            ->get(route('penyelenggara.laporan.index'))
            ->assertForbidden();

        // Penyelenggara membuka semua laporan
        $response = $this->actingAs($organizer)
            ->get(route('penyelenggara.laporan.index'));

        $response->assertStatus(200);
        $response->assertSee('Hackathon AI');
        $response->assertSee('UI/UX Challenge');
        // Total pendapatan lunas: 150.000 + 50.000 = Rp 200.000
        $response->assertSee('Rp 200.000');

        // Filter kategori Teknologi saja
        $filtered = $this->actingAs($organizer)
            ->get(route('penyelenggara.laporan.index', ['kategori' => 'Teknologi']));

        $filtered->assertStatus(200);
        $filtered->assertSee('Hackathon AI');
        $filtered->assertDontSee('UI/UX Challenge');
        $filtered->assertSee('Rp 150.000');
    }
}
