<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\Pengurus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $pengurus1 = Pengurus::first();
        $pengurus2 = Pengurus::skip(1)->first() ?? $pengurus1;

        $now = Carbon::now();

        // 1. Agenda Besok Hari (Untuk Menguji Peringatan H-1 Dashboard)
        $besok = Carbon::tomorrow();
        Agenda::create([
            'judul' => 'Rapat Koordinasi Asatidz & Evaluasi Mingguan',
            'deskripsi' => 'Pembahasan kedisiplinan santri di asrama dan persiapan ujian kitab kuning.',
            'mulai' => $besok->copy()->setHour(9)->setMinute(0),
            'selesai' => $besok->copy()->setHour(11)->setMinute(30),
            'lokasi' => 'Ruang Rapat Sekretariat Al Falah Putra',
            'pj_id' => $pengurus1 ? $pengurus1->id : null,
            'status' => 'rencana',
            'user_id' => $user ? $user->id : 1,
        ]);

        // 2. Agenda 5 Hari Mendatang
        $limaHariLagi = $now->copy()->addDays(5);
        Agenda::create([
            'judul' => 'Kajian Kitab Bulanan Bersama Alumni & Wali Santri',
            'deskripsi' => 'Pengajian umum pembacaan kitab Nashoihul Ibad di Masjid Jami Pondok.',
            'mulai' => $limaHariLagi->copy()->setHour(20)->setMinute(0),
            'selesai' => $limaHariLagi->copy()->setHour(22)->setMinute(0),
            'lokasi' => 'Masjid Jami Pondok Pesantren Al Falah Putra',
            'pj_id' => $pengurus2 ? $pengurus2->id : null,
            'status' => 'rencana',
            'user_id' => $user ? $user->id : 1,
        ]);

        // 3. Agenda 12 Hari Mendatang
        $duaBelasHari = $now->copy()->addDays(12);
        Agenda::create([
            'judul' => 'Ujian Akhir Semester Santri Tingkat Ula & Wustha',
            'deskripsi' => 'Ujian lisan hafalan dan bacaan kitab kuning seluruh kelas.',
            'mulai' => $duaBelasHari->copy()->setHour(8)->setMinute(0),
            'selesai' => $duaBelasHari->copy()->addDays(3)->setHour(12)->setMinute(0),
            'lokasi' => 'Komplek Gedung Pembelajaran Putra',
            'pj_id' => $pengurus1 ? $pengurus1->id : null,
            'status' => 'rencana',
            'user_id' => $user ? $user->id : 1,
        ]);

        // 4. Agenda Selesai Minggu Lalu
        $mingguLalu = $now->copy()->subDays(7);
        Agenda::create([
            'judul' => 'Peringatan Hari Santri Nasional',
            'deskripsi' => 'Upacara bendera dan apel akbar seluruh santri dan dewan guru.',
            'mulai' => $mingguLalu->copy()->setHour(7)->setMinute(30),
            'selesai' => $mingguLalu->copy()->setHour(10)->setMinute(0),
            'lokasi' => 'Lapangan Utama Pondok Pesantren Al Falah Putra',
            'pj_id' => $pengurus2 ? $pengurus2->id : null,
            'status' => 'selesai',
            'user_id' => $user ? $user->id : 1,
        ]);
    }
}
