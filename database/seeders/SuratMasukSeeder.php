<?php

namespace Database\Seeders;

use App\Models\KategoriSurat;
use App\Models\SuratMasuk;
use App\Models\User;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SuratMasukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $nomorService = app(NomorSuratService::class);
        $kategori = KategoriSurat::first();

        $suratData = [
            [
                'no_surat' => 'B-128/Kk.06.03/PP.00.7/10/2026',
                'tgl_surat' => Carbon::now()->subDays(5)->format('Y-m-d'),
                'tgl_diterima' => Carbon::now()->subDays(3)->format('Y-m-d'),
                'pengirim' => 'Kantor Kementerian Agama Kabupaten Muara Enim',
                'perihal' => 'Undangan Rapat Koordinasi Pimpinan Pondok Pesantren se-Kabupaten Muara Enim',
                'disposisi' => 'Dihadiri oleh pimpinan atau sekretaris pondok pesantren.',
                'status_disposisi' => 'diproses',
            ],
            [
                'no_surat' => '088/PCNU-ME/A.I/X/2026',
                'tgl_surat' => Carbon::now()->subDays(2)->format('Y-m-d'),
                'tgl_diterima' => Carbon::now()->subDay()->format('Y-m-d'),
                'pengirim' => 'Pengurus Cabang Nahdlatul Ulama Kabupaten Muara Enim',
                'perihal' => 'Permohonan Pendelegasian Santri Lomba Musabaqah Qira\'atil Kutub (MQK)',
                'disposisi' => null,
                'status_disposisi' => 'baru',
            ],
            [
                'no_surat' => '440/092/PKM-GLB/2026',
                'tgl_surat' => Carbon::now()->subDays(10)->format('Y-m-d'),
                'tgl_diterima' => Carbon::now()->subDays(8)->format('Y-m-d'),
                'pengirim' => 'Puskesmas Kecamatan Gelumbang',
                'perihal' => 'Pemberitahuan Jadwal Skrining dan Penyuluhan Kesehatan Santri',
                'disposisi' => 'Siapkan tempat di aula dan koordinasikan dengan pengurus asrama putra.',
                'status_disposisi' => 'selesai',
            ],
        ];

        foreach ($suratData as $data) {
            $noAgenda = $nomorService->generateNomorAgenda($data['tgl_diterima']);

            SuratMasuk::create([
                'no_agenda' => $noAgenda,
                'no_surat' => $data['no_surat'],
                'tgl_surat' => $data['tgl_surat'],
                'tgl_diterima' => $data['tgl_diterima'],
                'pengirim' => $data['pengirim'],
                'perihal' => $data['perihal'],
                'kategori_id' => $kategori ? $kategori->id : null,
                'file' => null,
                'disposisi' => $data['disposisi'],
                'status_disposisi' => $data['status_disposisi'],
                'user_id' => $user ? $user->id : 1,
            ]);
        }
    }
}
