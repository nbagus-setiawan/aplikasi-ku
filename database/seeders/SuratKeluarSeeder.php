<?php

namespace Database\Seeders;

use App\Models\KategoriSurat;
use App\Models\SuratKeluar;
use App\Models\User;
use App\Services\NomorSuratService;
use App\Services\TandaTanganService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SuratKeluarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $nomorService = app(NomorSuratService::class);
        $ttdService = app(TandaTanganService::class);

        $kategoriSpm = KategoriSurat::where('kode', 'like', '%SPm%')->first() ?? KategoriSurat::first();
        $kategoriSpb = KategoriSurat::where('kode', 'like', '%SPb%')->first() ?? KategoriSurat::first();

        // 1. Surat Keluar Final dengan Tanda Tangan Digital
        $now = Carbon::now();
        $noSurat1 = $nomorService->generateNomorSuratKeluar($kategoriSpb ? $kategoriSpb->kode : 'SPb.01', $now);
        $surat1 = SuratKeluar::create([
            'no_surat' => $noSurat1,
            'tgl_surat' => $now->format('Y-m-d'),
            'tujuan' => 'Seluruh Wali Santri Pondok Pesantren Al Falah Putra',
            'perihal' => 'Pemberitahuan Jadwal Kepulangan dan Liburan Santri',
            'kategori_id' => $kategoriSpb ? $kategoriSpb->id : 1,
            'isi' => "Sehubungan dengan telah berakhirnya kegiatan evaluasi pembelajaran semester genap tahun ajaran 1447-1448 H / 2026 M, bersama ini kami sampaikan kepada seluruh Bapak/Ibu Wali Santri bahwa kepulangan santri dapat dijemput mulai hari Ahad pagi.\n\nDiharapkan seluruh santri telah menyelesaikan seluruh tanggungan kitab dan kembali tepat waktu sesuai kalender pendidikan yang telah ditetapkan.",
            'nama_penandatangan' => 'K.H. Mursyidi',
            'jabatan_penandatangan' => 'Pengasuh Pondok Pesantren Al-Falah Putak',
            'file' => null,
            'status' => 'final',
            'metode_ttd' => 'digital',
            'status_ttd' => 'sudah',
            'kode_verifikasi' => strtoupper(Str::random(8)),
            'user_id' => $user ? $user->id : 1,
        ]);

        // Berikan data tanda tangan
        $dummySignaturePng = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkCAYAAAC9D2iBAAAgAElEQVR4Xu2dB5wdVfX/P3dur69tM9vzpvdOSCCFkEpRihQVxAqKFRsWFCuCv8WKDQsKKqJIEUQpCgqEIi2BBEghjfTelu312+7c/3Nn337fvHkvu2lZ3G/m+w8k2dy5c+4533K+53vOqQCj0Wg0Go1GozF9sHq9Ao1Go9FoNBrThkaHaTQajUaj0Wh0mEaj0Wg0Go3GtKH/A5k4n84P7Q5FAAAAAElFTkSuQmCC';
        $ttdService->prosesTtdDigital($surat1, $dummySignaturePng, '127.0.0.1');

        // 2. Surat Keluar Draft
        $noSurat2 = $nomorService->generateNomorSuratKeluar($kategoriSpm ? $kategoriSpm->kode : 'SPm.01', $now);
        SuratKeluar::create([
            'no_surat' => $noSurat2,
            'tgl_surat' => $now->format('Y-m-d'),
            'tujuan' => 'Kepala Kantor Kementerian Agama Kabupaten Muara Enim',
            'perihal' => 'Permohonan Validasi Data EMIS Pondok Pesantren',
            'kategori_id' => $kategoriSpm ? $kategoriSpm->id : 1,
            'isi' => "Dalam rangka pemutakhiran data kelembagaan dan statistik santri tahun berjalan pada sistem EMIS Kementerian Agama Kabupaten Muara Enim, kami memohon kesediaan Bapak untuk memvalidasi berkas kelembagaan terlampir.",
            'nama_penandatangan' => 'Nanda Bagus Setiawan',
            'jabatan_penandatangan' => 'Sekretaris Pondok Pesantren Al Falah Putra',
            'file' => null,
            'status' => 'draft',
            'metode_ttd' => null,
            'status_ttd' => 'belum',
            'kode_verifikasi' => strtoupper(Str::random(8)),
            'user_id' => $user ? $user->id : 1,
        ]);

        // 3. Surat Keluar dengan 3 Penandatangan Resmi & Tautan Aktif
        $noSurat3 = $nomorService->generateNomorSuratKeluar($kategoriSpm ? $kategoriSpm->kode : 'SPm.01', $now);
        $surat3 = SuratKeluar::create([
            'no_surat' => $noSurat3,
            'tgl_surat' => $now->format('Y-m-d'),
            'tujuan' => 'Seluruh Dewan Guru & Pengasuh Pondok',
            'perihal' => 'Undangan Rapat Koordinasi Tahunan',
            'ukuran_kertas' => 'a4',
            'margin' => 'standar',
            'kategori_id' => $kategoriSpm ? $kategoriSpm->id : 1,
            'isi' => "<p>Assalamu'alaikum Warahmatullahi Wabarakatuh,</p><p>Sehubungan dengan pelaksanaan evaluasi program kerja dan persiapan kegiatan semester baru, kami mengharap kehadiran Bapak/Ustadz pada rapat koordinasi yang insya Allah akan diselenggarakan pada:</p><p>&emsp;Hari / Tanggal : Sabtu, 17 Oktober 2026<br>&emsp;Waktu : 08.30 WIB - Selesai<br>&emsp;Tempat : Aula Utama Pondok Pesantren Al Falah Putra Putak</p><p>Mengingat pentingnya agenda tersebut, kami sangat mengharapkan kehadiran Bapak/Ustadz tepat pada waktunya.</p><p>Demikian undangan ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p><p>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</p>",
            'jumlah_penandatangan' => 3,
            'nama_penandatangan' => 'Nanda Bagus Setiawan',
            'jabatan_penandatangan' => 'Sekretaris Pondok Putra',
            'penandatangans' => [
                ['urutan' => 1, 'nama' => 'Nanda Bagus Setiawan', 'jabatan' => 'Sekretaris Pondok Putra'],
                ['urutan' => 2, 'nama' => 'Azwil Yoga Pratama', 'jabatan' => 'Bendahara Pondok Putra'],
                ['urutan' => 3, 'nama' => 'Danil Ali Sabila', 'jabatan' => 'Ketua Umum Pondok Putra'],
            ],
            'file' => null,
            'status' => 'draft',
            'metode_ttd' => 'digital',
            'status_ttd' => 'belum',
            'kode_verifikasi' => 'MC0CQXCH',
            'user_id' => $user ? $user->id : 1,
        ]);

        // Slot 1: Tautan aktif untuk token pengguna
        \App\Models\TandaTangan::create([
            'surat_keluar_id' => $surat3->id,
            'urutan' => 1,
            'nama_penandatangan' => 'Nanda Bagus Setiawan',
            'jabatan_penandatangan' => 'Sekretaris Pondok Putra',
            'token_sekali_pakai' => '0GFkPvnzH83gvXnhDjQN15GuW3P2mr0viHBNIcKcMtYGHNTw',
            'expires_at' => $now->copy()->addDays(30),
            'data_ttd' => null,
        ]);

        // Slot 2: Menunggu TTD
        \App\Models\TandaTangan::create([
            'surat_keluar_id' => $surat3->id,
            'urutan' => 2,
            'nama_penandatangan' => 'Azwil Yoga Pratama',
            'jabatan_penandatangan' => 'Bendahara Pondok Putra',
            'token_sekali_pakai' => Str::random(48),
            'expires_at' => $now->copy()->addDays(30),
            'data_ttd' => null,
        ]);

        // Slot 3: Menunggu TTD
        \App\Models\TandaTangan::create([
            'surat_keluar_id' => $surat3->id,
            'urutan' => 3,
            'nama_penandatangan' => 'Danil Ali Sabila',
            'jabatan_penandatangan' => 'Ketua Umum Pondok Putra',
            'token_sekali_pakai' => Str::random(48),
            'expires_at' => $now->copy()->addDays(30),
            'data_ttd' => null,
        ]);
    }
}
