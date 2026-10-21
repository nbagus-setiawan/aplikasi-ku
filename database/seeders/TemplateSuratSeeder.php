<?php

namespace Database\Seeders;

use App\Models\KategoriSurat;
use App\Models\TemplateSurat;
use Illuminate\Database\Seeder;

class TemplateSuratSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $katPermohonan = KategoriSurat::where('kode', 'SPm.01')->first();
        $katUndangan = KategoriSurat::where('kode', 'SU.06')->first() ?? KategoriSurat::where('kode', 'like', 'SU%')->first();
        $katPemberitahuan = KategoriSurat::where('kode', 'SPb.02')->first() ?? KategoriSurat::where('kode', 'like', 'SPb%')->first();
        $katKeterangan = KategoriSurat::where('kode', 'SKet.05')->first() ?? KategoriSurat::where('kode', 'like', 'SK%')->first();

        $templates = [
            [
                'nama' => 'Surat Permohonan (Narasumber / Bantuan / Izin)',
                'kategori_id' => $katPermohonan?->id,
                'kode_jenis_surat' => $katPermohonan?->kode ?? 'SPm.01',
                'tujuan_default' => 'Bapak/Ibu Pimpinan Instansi Terkait',
                'perihal_default' => 'Permohonan Fasilitasi / Narasumber Kegiatan',
                'isi_template' => '<p><em>Assalamu\'alaikum Warahmatullahi Wabarakatuh,</em></p>
<p style="text-indent: 28px;">Segala puji bagi Allah Subhanahu wa Ta\'ala, shalawat serta salam semoga senantiasa tercurah kepada junjungan kita Nabi Muhammad Shallallahu \'Alaihi wa Sallam beserta keluarga, sahabat, dan para pengikutnya hingga akhir zaman.</p>
<p style="text-indent: 28px;">Sehubungan dengan akan dilaksanakannya kegiatan pembinaan santri di Pondok Pesantren Al Falah Putra, bersama ini kami memohon kesediaan Bapak/Ibu untuk dapat memfasilitasi pada acara yang insyaAllah akan dilaksanakan pada:</p>
<table style="border-collapse: collapse; width: 100%; border: none; margin: 10px 0;">
    <tr><td style="border: none; width: 25%; padding: 3px 0;">Hari / Tanggal</td><td style="border: none; width: 3%;">:</td><td style="border: none; padding: 3px 0;"><strong>Sabtu, 24 Oktober 2026</strong></td></tr>
    <tr><td style="border: none; padding: 3px 0;">Waktu</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">08.30 WIB s.d. Selesai</td></tr>
    <tr><td style="border: none; padding: 3px 0;">Tempat</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">Aula Utama Pondok Pesantren Al Falah Putra</td></tr>
</table>
<p style="text-indent: 28px;">Demikian surat permohonan ini kami sampaikan. Atas perhatian, perkenan, dan kerja samanya kami haturkan terima kasih.</p>
<p><em>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p>',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Surat Undangan Resmi (Wali Santri / Asatidz / Tamu)',
                'kategori_id' => $katUndangan?->id,
                'kode_jenis_surat' => $katUndangan?->kode ?? 'SU.06',
                'tujuan_default' => 'Bapak/Ibu Wali Santri Pondok Pesantren Al Falah Putra',
                'perihal_default' => 'Undangan Silaturahmi dan Pertemuan Wali Santri',
                'isi_template' => '<p><em>Assalamu\'alaikum Warahmatullahi Wabarakatuh,</em></p>
<p style="text-indent: 28px;">Puji syukur kehadirat Allah Subhanahu wa Ta\'ala atas segala limpahan rahmat dan karunia-Nya. Shalawat dan salam semoga senantiasa tercurah kepada Rasulullah Muhammad Shallallahu \'Alaihi wa Sallam.</p>
<p style="text-indent: 28px;">Dalam rangka mempererat tali silaturahmi serta koordinasi pendidikan santri Pondok Pesantren Al Falah Putra, kami mengharap kehadiran Bapak/Ibu pada acara yang insyaAllah akan diselenggarakan pada:</p>
<table style="border-collapse: collapse; width: 100%; border: none; margin: 10px 0;">
    <tr><td style="border: none; width: 25%; padding: 3px 0;">Hari / Tanggal</td><td style="border: none; width: 3%;">:</td><td style="border: none; padding: 3px 0;"><strong>Ahad, 25 Oktober 2026</strong></td></tr>
    <tr><td style="border: none; padding: 3px 0;">Waktu</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">09.00 WIB s.d. Selesai</td></tr>
    <tr><td style="border: none; padding: 3px 0;">Tempat</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">Masjid / Aula Pondok Pesantren Al Falah Putra</td></tr>
    <tr><td style="border: none; padding: 3px 0;">Agenda</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">Silaturahmi &amp; Evaluasi Pembelajaran Santri</td></tr>
</table>
<p style="text-indent: 28px;">Mengingat pentingnya acara tersebut, kehadiran Bapak/Ibu tepat pada waktunya sangat kami harapkan. Demikian undangan ini kami sampaikan, atas perhatian dan kehadirannya kami ucapkan terima kasih.</p>
<p><em>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p>',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Surat Pemberitahuan / Edaran Pondok',
                'kategori_id' => $katPemberitahuan?->id,
                'kode_jenis_surat' => $katPemberitahuan?->kode ?? 'SPb.02',
                'tujuan_default' => 'Seluruh Wali Santri Pondok Pesantren Al Falah Putra',
                'perihal_default' => 'Pemberitahuan Kegiatan dan Ketentuan Pondok',
                'isi_template' => '<p><em>Assalamu\'alaikum Warahmatullahi Wabarakatuh,</em></p>
<p style="text-indent: 28px;">Semoga Bapak/Ibu Wali Santri senantiasa berada dalam lindungan Allah Subhanahu wa Ta\'ala dan diberikan kelancaran dalam menjalankan aktivitas sehari-hari.</p>
<p style="text-indent: 28px;">Sehubungan dengan agenda akademik Pondok Pesantren Al Falah Putra, bersama ini kami beritahukan beberapa ketentuan berikut:</p>
<ol>
    <li>Kegiatan ujian semester ganjil akan dilaksanakan mulai tanggal <strong>10 s.d. 18 November 2026</strong>.</li>
    <li>Selama masa ujian berlangsung, kegiatan perizinan pulang santri dibatasi kecuali untuk keperluan mendesak.</li>
    <li>Diharapkan seluruh wali santri turut mendoakan kelancaran studi putra-putranya di pondok pesantren.</li>
</ol>
<p style="text-indent: 28px;">Demikian surat pemberitahuan ini kami sampaikan untuk dapat dimaklumi. Atas perhatian dan kerja sama yang baik kami ucapkan terima kasih.</p>
<p><em>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p>',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Surat Keterangan Aktif Santri / Tugas',
                'kategori_id' => $katKeterangan?->id,
                'kode_jenis_surat' => $katKeterangan?->kode ?? 'SKet.05',
                'tujuan_default' => 'Pihak yang Berkepentingan',
                'perihal_default' => 'Surat Keterangan Aktif Belajar Santri',
                'isi_template' => '<p><em>Assalamu\'alaikum Warahmatullahi Wabarakatuh,</em></p>
<p style="text-indent: 28px;">Pengurus Sekretariat Pondok Pesantren Al Falah Putra (di bawah naungan Pondok Pesantren Al-Falah Putak) dengan ini menerangkan bahwa:</p>
<table style="border-collapse: collapse; width: 100%; border: none; margin: 10px 0;">
    <tr><td style="border: none; width: 25%; padding: 3px 0;">Nama Lengkap</td><td style="border: none; width: 3%;">:</td><td style="border: none; padding: 3px 0;"><strong>[Nama Lengkap Santri]</strong></td></tr>
    <tr><td style="border: none; padding: 3px 0;">Nomor Induk Santri (NIS)</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">[NIS Santri]</td></tr>
    <tr><td style="border: none; padding: 3px 0;">Tempat, Tanggal Lahir</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">[Tempat, Tanggal Lahir]</td></tr>
    <tr><td style="border: none; padding: 3px 0;">Kelas / Kamar</td><td style="border: none;">:</td><td style="border: none; padding: 3px 0;">[Kelas] / [Kamar]</td></tr>
</table>
<p style="text-indent: 28px;">Adalah benar-benar santri aktif yang sedang menempuh pendidikan dan bermukim di Pondok Pesantren Al Falah Putra pada Tahun Ajaran berjalan.</p>
<p style="text-indent: 28px;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dapat dipergunakan sebagaimana mestinya.</p>
<p><em>Wassalamu\'alaikum Warahmatullahi Wabarakatuh.</em></p>',
                'is_aktif' => true,
            ],
        ];

        foreach ($templates as $tpl) {
            TemplateSurat::updateOrCreate(
                ['nama' => $tpl['nama']],
                $tpl
            );
        }
    }
}
