<?php

namespace Tests\Feature;

use App\Models\Notifikasi;
use App\Models\Pengumuman;
use App\Models\PengumumanKomentar;
use App\Models\PengumumanLike;
use App\Models\PengumumanShare;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengumumanInteraksiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Pengumuman $pengumuman;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Sekretaris Al-Falah',
            'email' => 'sekretaris@alfalah.test',
        ]);

        $this->pengumuman = Pengumuman::create([
            'judul' => 'Penerimaan Santri Baru Pondok Pesantren Al-Falah Putra',
            'slug' => 'penerimaan-santri-baru-al-falah',
            'isi' => 'Pendaftaran santri baru telah dibuka secara resmi.',
            'kategori' => 'kegiatan',
            'tgl_tayang' => now()->subDay(),
            'status' => 'terbit',
            'user_id' => $this->admin->id,
        ]);
    }

    public function test_halaman_pengumuman_dapat_diakses_publik(): void
    {
        $response = $this->get(route('publik.pengumuman.show', $this->pengumuman->slug));

        $response->assertStatus(200);
        $response->assertSee('Penerimaan Santri Baru Pondok Pesantren Al-Falah Putra');
        $response->assertSee('Suka');
        $response->assertSee('Komentar');
        $response->assertSee('Bagikan');
    }

    public function test_publik_dapat_menyukai_dan_batal_menyukai_pengumuman(): void
    {
        // 1. Suka Pengumuman
        $response = $this->postJson(route('publik.pengumuman.like', $this->pengumuman->slug));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('pengumuman_likes', [
            'pengumuman_id' => $this->pengumuman->id,
        ]);

        // Verifikasi notifikasi untuk panel sekretaris masuk
        $this->assertDatabaseHas('notifikasis', [
            'tipe' => 'like',
            'pengumuman_id' => $this->pengumuman->id,
            'is_dibaca' => false,
        ]);

        // 2. Batalkan Suka (Unlike)
        $responseUnlike = $this->postJson(route('publik.pengumuman.like', $this->pengumuman->slug));

        $responseUnlike->assertStatus(200)
            ->assertJson([
                'success' => true,
                'liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertDatabaseMissing('pengumuman_likes', [
            'pengumuman_id' => $this->pengumuman->id,
        ]);
    }

    public function test_publik_dapat_mengirim_komentar_tanpa_nama(): void
    {
        $response = $this->postJson(route('publik.pengumuman.komentar', $this->pengumuman->slug), [
            'nama' => '',
            'isi' => 'Semoga berkah dan lancar untuk seluruh santri baru!',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'komentars_count' => 1,
            ]);

        $this->assertDatabaseHas('pengumuman_komentars', [
            'pengumuman_id' => $this->pengumuman->id,
            'nama' => null,
            'isi' => 'Semoga berkah dan lancar untuk seluruh santri baru!',
        ]);

        // Notifikasi bertuliskan User Publik
        $this->assertDatabaseHas('notifikasis', [
            'tipe' => 'komentar',
            'pengumuman_id' => $this->pengumuman->id,
            'judul' => 'Komentar Baru: User Publik',
            'is_dibaca' => false,
        ]);
    }

    public function test_publik_dapat_mengirim_komentar_dengan_nama_kustom(): void
    {
        $response = $this->postJson(route('publik.pengumuman.komentar', $this->pengumuman->slug), [
            'nama' => 'Ustadz Abdullah',
            'isi' => 'Ahlan wa sahlan para santri penerus perjuangan umat.',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'komentars_count' => 1,
            ]);

        $this->assertDatabaseHas('pengumuman_komentars', [
            'pengumuman_id' => $this->pengumuman->id,
            'nama' => 'Ustadz Abdullah',
        ]);

        // Notifikasi bertuliskan nama yang diinput
        $this->assertDatabaseHas('notifikasis', [
            'tipe' => 'komentar',
            'pengumuman_id' => $this->pengumuman->id,
            'judul' => 'Komentar Baru: Ustadz Abdullah',
        ]);
    }

    public function test_komentar_tanpa_isi_gagal_validasi(): void
    {
        $response = $this->postJson(route('publik.pengumuman.komentar', $this->pengumuman->slug), [
            'nama' => 'Ahmad',
            'isi' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['isi']);
    }

    public function test_publik_dapat_mencatat_pembagian_berita(): void
    {
        $response = $this->postJson(route('publik.pengumuman.share', $this->pengumuman->slug), [
            'platform' => 'whatsapp',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'shares_count' => 1,
            ]);

        $this->assertDatabaseHas('pengumuman_shares', [
            'pengumuman_id' => $this->pengumuman->id,
            'platform' => 'whatsapp',
        ]);

        $this->assertDatabaseHas('notifikasis', [
            'tipe' => 'share',
            'pengumuman_id' => $this->pengumuman->id,
        ]);
    }

    public function test_tamu_tidak_dapat_mengakses_api_notifikasi_admin(): void
    {
        $this->getJson(route('admin.notifikasi.index'))->assertStatus(401);
    }

    public function test_admin_dapat_mengambil_dan_menandai_notifikasi_sebagai_dibaca(): void
    {
        $notif = Notifikasi::create([
            'judul' => 'Suka Baru',
            'pesan' => 'User Publik menyukai berita',
            'tipe' => 'like',
            'pengumuman_id' => $this->pengumuman->id,
            'is_dibaca' => false,
        ]);

        // 1. Ambil Notifikasi
        $response = $this->actingAs($this->admin)->getJson(route('admin.notifikasi.index'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 1,
            ]);

        // 2. Tandai Dibaca (Pratinjau)
        $responseBaca = $this->actingAs($this->admin)->patchJson(route('admin.notifikasi.baca', $notif->id));

        $responseBaca->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertDatabaseHas('notifikasis', [
            'id' => $notif->id,
            'is_dibaca' => true,
        ]);

        // 3. Tandai Semua Dibaca
        Notifikasi::create([
            'judul' => 'Notif 2',
            'pesan' => 'Pesan 2',
            'tipe' => 'komentar',
            'is_dibaca' => false,
        ]);

        $responseSemua = $this->actingAs($this->admin)->postJson(route('admin.notifikasi.baca-semua'));

        $responseSemua->assertStatus(200)
            ->assertJson([
                'success' => true,
                'unread_count' => 0,
            ]);

        $this->assertEquals(0, Notifikasi::belumDibaca()->count());
    }
}
