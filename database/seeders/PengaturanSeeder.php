<?php

namespace Database\Seeders;

use App\Models\Pengaturan;
use Illuminate\Database\Seeder;

class PengaturanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'nama_situs' => 'SAP | SEKRETARIS AL-FALAH PUTRA',
            'nama_lembaga' => 'Pondok Pesantren Al Falah Putra',
            'kode_lembaga' => 'AF-PA',
            'pola_nomor_surat' => '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}',
            'kop_yayasan' => 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK',
            'kop_nama' => 'PONDOK PESANTREN AL-FALAH PUTRA',
            'kop_alamat' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171',
            'kop_kontak' => 'No. Telp/HP: 0812-7890-3117 | Email: sekretariat@alfalahputra.sch.id',
            'warna_tema' => '#059669',
            'telepon' => '0812-7890-3117',
            'email' => 'sekretariat@alfalahputra.sch.id',
            'alamat' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171',
            'ukuran_kertas' => 'a4',
            'orientasi_kertas' => 'portrait',
            'margin_kertas' => 'standar',
            'footer_teks' => '',
            'logo' => 'logo.png',
        ];

        foreach ($defaults as $key => $value) {
            Pengaturan::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }
}
