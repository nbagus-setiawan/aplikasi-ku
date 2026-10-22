<?php

namespace Tests\Feature;

use App\Models\Pengumuman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengumumanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'sekretaris@alfalahputra.test',
        ]);
        Storage::fake('public');
    }

    public function test_tamu_tidak_bisa_mengakses_modul_pengumuman_admin()
    {
        $response = $this->get(route('admin.pengumuman.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_bisa_melihat_daftar_pengumuman_admin()
    {
        Pengumuman::create([
            'judul' => 'Maklumat Santri Baru',
            'slug' => 'maklumat-santri-baru',
            'kategori' => 'santri',
            'isi' => 'Rincian maklumat',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.pengumuman.index'));
        $response->assertStatus(200);
        $response->assertSee('Maklumat Santri Baru');
    }

    public function test_sekretaris_bisa_membuat_pengumuman_baru_dengan_gambar()
    {
        $file = UploadedFile::fake()->create('maklumat.jpg', 100, 'image/jpeg');

        $payload = [
            'judul' => 'Jadwal Libur Awal Ramadhan',
            'kategori' => 'santri',
            'isi' => 'Seluruh kegiatan asrama diliburkan selama awal Ramadhan.',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'tgl_kedaluwarsa' => Carbon::today()->addDays(30)->toDateString(),
            'is_pinned' => 1,
            'status' => 'terbit',
            'gambar' => $file,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.pengumuman.store'), $payload);
        $response->assertRedirect(route('admin.pengumuman.index'));

        $pengumuman = Pengumuman::first();
        $this->assertNotNull($pengumuman);
        $this->assertEquals('Jadwal Libur Awal Ramadhan', $pengumuman->judul);
        $this->assertEquals('jadwal-libur-awal-ramadhan', $pengumuman->slug);
        $this->assertTrue($pengumuman->is_pinned);
        $this->assertNotNull($pengumuman->gambar);
        Storage::disk('public')->assertExists($pengumuman->gambar);
    }

    public function test_sekretaris_bisa_memperbarui_pengumuman()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Judul Lama',
            'slug' => 'judul-lama',
            'kategori' => 'umum',
            'isi' => 'Isi teks lama',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $payload = [
            'judul' => 'Judul Baru Diperbarui',
            'kategori' => 'wali',
            'isi' => 'Isi teks baru terbit',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
        ];

        $response = $this->actingAs($this->user)->put(route('admin.pengumuman.update', $pengumuman), $payload);
        $response->assertRedirect(route('admin.pengumuman.index'));

        $pengumuman->refresh();
        $this->assertEquals('Judul Baru Diperbarui', $pengumuman->judul);
        $this->assertEquals('wali', $pengumuman->kategori);
        $this->assertEquals('terbit', $pengumuman->status);
    }

    public function test_sekretaris_bisa_menghapus_pengumuman_soft_delete()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Pengumuman Dihapus',
            'slug' => 'pengumuman-dihapus',
            'kategori' => 'umum',
            'isi' => 'Akan segera dihapus',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.pengumuman.destroy', $pengumuman));
        $response->assertRedirect(route('admin.pengumuman.index'));

        $this->assertSoftDeleted('pengumumans', ['id' => $pengumuman->id]);
    }

    public function test_halaman_publik_pengumuman_hanya_menampilkan_pengumuman_terbit_dan_belum_kedaluwarsa()
    {
        // 1. Terbit aktif
        Pengumuman::create([
            'judul' => 'Pengumuman Terbit Aktif',
            'slug' => 'pengumuman-terbit-aktif',
            'kategori' => 'umum',
            'isi' => 'Konten aktif untuk publik',
            'tgl_tayang' => Carbon::today()->subDay()->toDateString(),
            'tgl_kedaluwarsa' => Carbon::today()->addDays(5)->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        // 2. Draft (tidak boleh tampil di publik)
        Pengumuman::create([
            'judul' => 'Pengumuman Rahasia Draft',
            'slug' => 'pengumuman-rahasia-draft',
            'kategori' => 'umum',
            'isi' => 'Draf internal',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        // 3. Kedaluwarsa (tidak boleh tampil di publik)
        Pengumuman::create([
            'judul' => 'Pengumuman Masa Lalu',
            'slug' => 'pengumuman-masa-lalu',
            'kategori' => 'umum',
            'isi' => 'Sudah kedaluwarsa',
            'tgl_tayang' => Carbon::today()->subDays(10)->toDateString(),
            'tgl_kedaluwarsa' => Carbon::today()->subDay()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('publik.pengumuman.index'));
        $response->assertStatus(200);
        $response->assertSee('Pengumuman Terbit Aktif');
        $response->assertDontSee('Pengumuman Rahasia Draft');
        $response->assertDontSee('Pengumuman Masa Lalu');
    }

    public function test_detail_publik_pengumuman_bisa_diakses_jika_terbit()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Rincian Acara Khataman',
            'slug' => 'rincian-acara-khataman',
            'kategori' => 'akademik',
            'isi' => 'Acara khataman akbar santri diselenggarakan di masjid jami.',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('publik.pengumuman.show', $pengumuman->slug));
        $response->assertStatus(200);
        $response->assertSee('Rincian Acara Khataman');
        $response->assertSee('Acara khataman akbar santri');
    }

    public function test_detail_publik_pengumuman_mengembalikan_404_jika_masih_draft()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Rencana Kerja Internal',
            'slug' => 'rencana-kerja-internal',
            'kategori' => 'umum',
            'isi' => 'Hanya untuk pengurus',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'draft',
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('publik.pengumuman.show', $pengumuman->slug));
        $response->assertStatus(404);
    }

    public function test_tautan_singkat_pengumuman_mengarahkan_ke_detail_publik()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Maklumat Libur Santri',
            'slug' => 'maklumat-libur-santri',
            'kategori' => 'santri',
            'isi' => 'Rincian liburan santri',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        // Cek akses via /p/{id}
        $responseId = $this->get('/p/' . $pengumuman->id);
        $responseId->assertRedirect(route('publik.pengumuman.show', $pengumuman->slug));

        // Cek akses via /p/{slug}
        $responseSlug = $this->get('/p/' . $pengumuman->slug);
        $responseSlug->assertRedirect(route('publik.pengumuman.show', $pengumuman->slug));
    }

    public function test_user_publik_bisa_memberikan_like_dan_membatalkan_like_secara_dinamis()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Maklumat Pengajian Akbar',
            'slug' => 'maklumat-pengajian-akbar',
            'kategori' => 'umum',
            'isi' => 'Mengharap kehadiran kaum muslimin',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        // Like pertama kali -> bertambah
        $res1 = $this->postJson(route('publik.pengumuman.like', $pengumuman->slug));
        $res1->assertOk()
            ->assertJson([
                'success' => true,
                'liked' => true,
                'likes_count' => 1,
            ]);
        $this->assertDatabaseHas('pengumuman_likes', [
            'pengumuman_id' => $pengumuman->id,
        ]);

        // Like kedua kali dari IP yang sama -> toggle batal suka (unlike)
        $res2 = $this->postJson(route('publik.pengumuman.like', $pengumuman->slug));
        $res2->assertOk()
            ->assertJson([
                'success' => true,
                'liked' => false,
                'likes_count' => 0,
            ]);
        $this->assertDatabaseMissing('pengumuman_likes', [
            'pengumuman_id' => $pengumuman->id,
        ]);
    }

    public function test_user_publik_bisa_mengirimkan_komentar_dan_tersimpan_di_database()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Peringatan Haul Akbar',
            'slug' => 'peringatan-haul-akbar',
            'kategori' => 'umum',
            'isi' => 'Acara haul tahunan pondok',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        $res = $this->postJson(route('publik.pengumuman.komentar', $pengumuman->slug), [
            'nama' => 'Wali Santri Ahmad',
            'isi' => 'Semoga acara berjalan lancar dan penuh berkah. Aamiin.',
        ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'komentars_count' => 1,
                'komentar' => [
                    'nama' => 'Wali Santri Ahmad',
                ],
            ]);

        $this->assertDatabaseHas('pengumuman_komentars', [
            'pengumuman_id' => $pengumuman->id,
            'nama' => 'Wali Santri Ahmad',
            'is_tampil' => true,
        ]);
    }

    public function test_user_publik_bisa_mencatat_aktivitas_share_secara_dinamis()
    {
        $pengumuman = Pengumuman::create([
            'judul' => 'Kajian Rutin Malam Jumat',
            'slug' => 'kajian-rutin-malam-jumat',
            'kategori' => 'umum',
            'isi' => 'Kajian kitab kuning',
            'tgl_tayang' => Carbon::today()->toDateString(),
            'status' => 'terbit',
            'user_id' => $this->user->id,
        ]);

        $res = $this->postJson(route('publik.pengumuman.share', $pengumuman->slug), [
            'platform' => 'whatsapp',
        ]);

        $res->assertOk()
            ->assertJson([
                'success' => true,
                'shares_count' => 1,
            ]);

        $this->assertDatabaseHas('pengumuman_shares', [
            'pengumuman_id' => $pengumuman->id,
            'platform' => 'whatsapp',
        ]);
    }
}
