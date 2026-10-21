<?php

namespace Database\Seeders;

use App\Models\KategoriSurat;
use Illuminate\Database\Seeder;

class KategoriSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Surat Permohonan',
                'kode' => 'SPm.01',
                'keterangan' => 'Surat permohonan izin/bantuan/fasilitas/audiensi',
            ],
            [
                'nama' => 'Surat Pemberitahuan',
                'kode' => 'SPb.02',
                'keterangan' => 'Surat pemberitahuan kegiatan/informasi resmi pesantren',
            ],
            [
                'nama' => 'Surat Mandat',
                'kode' => 'SM.03',
                'keterangan' => 'Surat mandat penugasan delegasi kafilah lomba/utusan santri',
            ],
            [
                'nama' => 'Surat Rekomendasi',
                'kode' => 'SR.04',
                'keterangan' => 'Surat rekomendasi santri atau pengurus untuk studi lanjut/beasiswa',
            ],
            [
                'nama' => 'Surat Keterangan',
                'kode' => 'SKet.05',
                'keterangan' => 'Surat keterangan aktif belajar, mukim di pondok, atau domisili santri',
            ],
            [
                'nama' => 'Surat Undangan',
                'kode' => 'SU.06',
                'keterangan' => 'Surat undangan acara haul, pertemuan wali santri, atau rapat asatidz',
            ],
            [
                'nama' => 'Surat Tugas',
                'kode' => 'ST.07',
                'keterangan' => 'Surat penugasan asatidz, pengurus, atau panitia kegiatan pondok',
            ],
            [
                'nama' => 'Surat Keputusan',
                'kode' => 'SK.08',
                'keterangan' => 'Surat keputusan pimpinan, penetapan asatidz/pengurus, atau kebijakan pesantren',
            ],
            [
                'nama' => 'Surat Pengantar',
                'kode' => 'SPg.09',
                'keterangan' => 'Surat pengantar dokumen resmi ke instansi pemerintah/Kemenag/organisasi',
            ],
            [
                'nama' => 'Surat Panggilan / Peringatan',
                'kode' => 'SP.10',
                'keterangan' => 'Surat panggilan atau peringatan santri dan wali santri terkait ketertiban',
            ],
            [
                'nama' => 'Surat Pernyataan',
                'kode' => 'SPn.11',
                'keterangan' => 'Surat pernyataan resmi kesanggupan, keabsahan berkas, atau komitmen',
            ],
            [
                'nama' => 'Surat Perjanjian Kerjasama (MoU)',
                'kode' => 'SPj.12',
                'keterangan' => 'Nota kesepahaman dan perjanjian kerjasama kelembagaan pondok pesantren',
            ],
            [
                'nama' => 'Surat Kuasa',
                'kode' => 'SKu.13',
                'keterangan' => 'Surat kuasa pengurusan administrasi atau perwakilan resmi pondok',
            ],
            [
                'nama' => 'Berita Acara',
                'kode' => 'BA.14',
                'keterangan' => 'Berita acara serah terima jabatan, hasil musyawarah, atau inventaris',
            ],
            [
                'nama' => 'Nota Dinas',
                'kode' => 'ND.15',
                'keterangan' => 'Komunikasi kedinasan internal antarbagian atau divisi kepengurusan',
            ],
            [
                'nama' => 'Surat Izin / Dispensasi',
                'kode' => 'SI.16',
                'keterangan' => 'Surat izin pulang santri, perizinan khusus, atau dispensasi kegiatan',
            ],
            [
                'nama' => 'Surat Edaran',
                'kode' => 'SE.17',
                'keterangan' => 'Surat edaran maklumat umum pimpinan pondok untuk asatidz, santri, dan wali',
            ],
            [
                'nama' => 'Syahadah / Sertifikat',
                'kode' => 'SY.18',
                'keterangan' => 'Syahadah kelulusan, sertifikat khataman kitab, atau piagam tanda tamat',
            ],
            [
                'nama' => 'Surat Keterangan Lulus (SKL)',
                'kode' => 'SKL.19',
                'keterangan' => 'Surat keterangan kelulusan sementara santri sebelum syahadah terbit',
            ],
            [
                'nama' => 'Surat Keterangan Pindah (Mutasi)',
                'kode' => 'SKP.20',
                'keterangan' => 'Surat keterangan pindah belajar atau mutasi santri ke pesantren/sekolah lain',
            ],
            [
                'nama' => 'Surat Keterangan Berkelakuan Baik',
                'kode' => 'SKBB.21',
                'keterangan' => 'Surat keterangan akhlakul karimah dan catatan disiplin santri di pondok',
            ],
            [
                'nama' => 'Surat Balasan',
                'kode' => 'SB.22',
                'keterangan' => 'Surat balasan resmi atas permohonan, undangan, atau surat dari pihak luar',
            ],
        ];

        foreach ($kategoris as $item) {
            KategoriSurat::updateOrCreate(
                ['kode' => $item['kode']],
                [
                    'nama' => $item['nama'],
                    'keterangan' => $item['keterangan'],
                ]
            );
        }
    }
}
