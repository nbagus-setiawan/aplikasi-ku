<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\Pengurus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AgendaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Pengurus $pengurus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->pengurus = Pengurus::create([
            'nama' => 'Ust. Ahmad Fauzi',
            'jabatan' => 'Kepala Madrasah Diniyah',
            'divisi' => 'Pendidikan',
            'no_hp' => '081234567890',
            'periode_mulai' => '2026-01-01',
            'periode_selesai' => '2028-12-31',
            'is_aktif' => true,
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_agenda(): void
    {
        $response = $this->get(route('admin.agenda.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_melihat_daftar_agenda_dan_kalender(): void
    {
        Agenda::create([
            'judul' => 'Rapat Bulanan Asatidz',
            'deskripsi' => 'Rapat evaluasi kurikulum',
            'mulai' => Carbon::now()->addDays(2),
            'selesai' => Carbon::now()->addDays(2)->addHours(2),
            'lokasi' => 'Aula Pertemuan',
            'pj_id' => $this->pengurus->id,
            'status' => 'rencana',
            'user_id' => $this->user->id,
        ]);

        // Mode Daftar
        $responseDaftar = $this->actingAs($this->user)->get(route('admin.agenda.index', ['view' => 'daftar']));
        $responseDaftar->assertStatus(200);
        $responseDaftar->assertSee('Rapat Bulanan Asatidz');
        $responseDaftar->assertSee('Aula Pertemuan');

        // Mode Kalender
        $responseKalender = $this->actingAs($this->user)->get(route('admin.agenda.index', ['view' => 'kalender']));
        $responseKalender->assertStatus(200);
        $responseKalender->assertSee('Rapat Bulanan Asatidz');
    }

    public function test_sekretaris_dapat_menambahkan_agenda_baru(): void
    {
        $mulai = Carbon::now()->addDays(3)->format('Y-m-d\TH:i');
        $selesai = Carbon::now()->addDays(3)->addHours(3)->format('Y-m-d\TH:i');

        $payload = [
            'judul' => 'Kajian Rutin Malam Jumat',
            'deskripsi' => 'Pembacaan Maulid Simthudduror dan Ratib Al-Haddad',
            'mulai' => $mulai,
            'selesai' => $selesai,
            'lokasi' => 'Masjid Jami',
            'pj_id' => $this->pengurus->id,
            'status' => 'rencana',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.agenda.store'), $payload);

        $this->assertDatabaseHas('agendas', [
            'judul' => 'Kajian Rutin Malam Jumat',
            'lokasi' => 'Masjid Jami',
            'status' => 'rencana',
        ]);

        $agenda = Agenda::first();
        $response->assertRedirect(route('admin.agenda.show', $agenda));
    }

    public function test_sekretaris_dapat_mengubah_agenda(): void
    {
        $agenda = Agenda::create([
            'judul' => 'Agenda Awal',
            'mulai' => Carbon::now()->addDay(),
            'selesai' => Carbon::now()->addDay()->addHours(2),
            'lokasi' => 'Gedung Lama',
            'pj_id' => $this->pengurus->id,
            'status' => 'rencana',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.agenda.update', $agenda), [
            'judul' => 'Agenda Diperbarui',
            'mulai' => Carbon::now()->addDay()->format('Y-m-d\TH:i'),
            'selesai' => Carbon::now()->addDay()->addHours(3)->format('Y-m-d\TH:i'),
            'lokasi' => 'Gedung Baru',
            'status' => 'berjalan',
        ]);

        $response->assertRedirect(route('admin.agenda.show', $agenda));

        $this->assertDatabaseHas('agendas', [
            'id' => $agenda->id,
            'judul' => 'Agenda Diperbarui',
            'lokasi' => 'Gedung Baru',
            'status' => 'berjalan',
        ]);
    }

    public function test_sekretaris_dapat_menghapus_agenda_dengan_soft_delete(): void
    {
        $agenda = Agenda::create([
            'judul' => 'Agenda Hapus',
            'mulai' => Carbon::now()->addDays(5),
            'selesai' => Carbon::now()->addDays(5)->addHours(2),
            'lokasi' => 'Lokasi',
            'status' => 'rencana',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.agenda.destroy', $agenda));

        $response->assertRedirect(route('admin.agenda.index'));
        $this->assertSoftDeleted('agendas', ['id' => $agenda->id]);
    }

    public function test_peringatan_agenda_h_minus_satu_berfungsi(): void
    {
        $besok = Carbon::tomorrow();

        $agendaBesok = Agenda::create([
            'judul' => 'Peringatan Haul Akbar Besok',
            'mulai' => $besok->copy()->setHour(8)->setMinute(0),
            'selesai' => $besok->copy()->setHour(12)->setMinute(0),
            'lokasi' => 'Aula Utama Pondok Pesantren Al-Falah Putak',
            'status' => 'rencana',
            'user_id' => $this->user->id,
        ]);

        $agendasH1 = Agenda::hMinusSatu()->get();

        $this->assertTrue($agendasH1->contains($agendaBesok));

        // Verifikasi tampil di dashboard
        $responseDashboard = $this->actingAs($this->user)->get(route('dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee('Peringatan Agenda H-1');
        $responseDashboard->assertSee('Peringatan Haul Akbar Besok');
    }
}
