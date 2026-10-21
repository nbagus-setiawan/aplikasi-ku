<?php

namespace Database\Seeders;

use App\Models\Pengumuman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();

        $data = [
            [
                'judul' => 'Maklumat Pendaftaran Santri Baru Tahun Ajaran 1448/1449 H',
                'slug' => 'maklumat-pendaftaran-santri-baru-1448-h',
                'isi' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nDiberitahukan kepada segenap kaum muslimin, wali santri, dan calon santri Pondok Pesantren Al-Falah Putra, bahwa pendaftaran santri baru untuk jenjang Ula dan Wustha resmi dibuka mulai tanggal 1 Syawal hingga 20 Dzulqo'dah.\n\nPersyaratan administrasi:\n1. Mengisi formulir pendaftaran resmi sekretariat.\n2. Menyerahkan fotokopi kartu keluarga dan akta kelahiran.\n3. Pas foto hitam putih ukuran 3x4 (4 lembar).\n4. Mengikuti tes baca Al-Quran dan tes dasar akhlak.\n\nInformasi lebih lanjut dapat menghubungi nomor telepon sekretariat atau datang langsung ke kantor administrasi pada jam dinas.",
                'kategori' => 'santri',
                'tgl_tayang' => Carbon::now()->subDays(5)->toDateString(),
                'tgl_kedaluwarsa' => Carbon::now()->addMonths(2)->toDateString(),
                'is_pinned' => true,
                'status' => 'terbit',
                'user_id' => $user?->id,
            ],
            [
                'judul' => 'Jadwal Kepulangan dan Libur Hari Raya Idul Fitri 1448 H',
                'slug' => 'jadwal-kepulangan-libur-idul-fitri-1448-h',
                'isi' => "Diberitahukan kepada seluruh wali santri bahwa kegiatan belajar mengajar dan pengajian kitab ramadhan akan diliburkan terhitung mulai tanggal 20 Ramadhan hingga 10 Syawal.\n\nWali santri diharapkan menjemput santri tepat waktu sesuai dengan nomor kamar masing-masing untuk menjaga ketertiban arus lalu lintas asrama putra.\n\nWassalam,\nSekretariat Pondok Pesantren Al-Falah Putra.",
                'kategori' => 'wali',
                'tgl_tayang' => Carbon::now()->subDays(2)->toDateString(),
                'tgl_kedaluwarsa' => Carbon::now()->addDays(20)->toDateString(),
                'is_pinned' => false,
                'status' => 'terbit',
                'user_id' => $user?->id,
            ],
            [
                'judul' => 'Pelaksanaan Ujian Caturwulan I Madrasah Diniyah',
                'slug' => 'pelaksanaan-ujian-caturwulan-i-madrasah-diniyah',
                'isi' => "Diumumkan kepada segenap santri bahwa Imtihan / Ujian Tulis dan Lisan Caturwulan I akan diselenggarakan mulai hari Sabtu depan. Santri diwajibkan menyelesaikan seluruh muthola'ah kitab dan menjaga kehadiran jamaah shalat fardhu.",
                'kategori' => 'akademik',
                'tgl_tayang' => Carbon::now()->subDay()->toDateString(),
                'tgl_kedaluwarsa' => Carbon::now()->addDays(14)->toDateString(),
                'is_pinned' => false,
                'status' => 'terbit',
                'user_id' => $user?->id,
            ],
            [
                'judul' => 'Draf Rencana Renovasi Aula Pertemuan Asrama Putra',
                'slug' => 'draf-rencana-renovasi-aula-pertemuan',
                'isi' => "Catatan internal sekretariat mengenai rencana renovasi aula pertemuan asrama putra dalam rangka persiapan wisuda santri.",
                'kategori' => 'umum',
                'tgl_tayang' => Carbon::now()->toDateString(),
                'tgl_kedaluwarsa' => null,
                'is_pinned' => false,
                'status' => 'draft', // Draf agar bisa ditest di feature test
                'user_id' => $user?->id,
            ],
        ];

        foreach ($data as $item) {
            Pengumuman::updateOrCreate(['slug' => $item['slug']], $item);
        }
    }
}
