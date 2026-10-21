<?php

namespace App\Services;

class HalamanBlockService
{
    /**
     * Daftar tipe blok yang diperbolehkan.
     */
    public const ALLOWED_BLOCK_TYPES = [
        'hero',
        'profil',
        'statistik',
        'program',
        'pengumuman',
        'kontak',
    ];

    /**
     * Sanitasi rekursif untuk data blok agar bebas dari skrip jahat / XSS.
     */
    public function sanitizeBlocks(array $blocks): array
    {
        $sanitized = [];

        foreach ($blocks as $block) {
            if (!is_array($block) || !isset($block['type'])) {
                continue;
            }

            $type = strtolower(trim($block['type']));
            if (!in_array($type, self::ALLOWED_BLOCK_TYPES, true)) {
                continue;
            }

            $data = isset($block['data']) && is_array($block['data']) ? $block['data'] : [];

            $sanitized[] = [
                'id' => $this->sanitizeString($block['id'] ?? uniqid('block_')),
                'type' => $type,
                'data' => $this->sanitizeData($data),
            ];
        }

        return $sanitized;
    }

    /**
     * Sanitasi array asosiatif data properti blok.
     */
    protected function sanitizeData(array $data): array
    {
        $clean = [];

        foreach ($data as $key => $val) {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)$key);

            if (is_array($val)) {
                $clean[$cleanKey] = $this->sanitizeData($val);
            } else {
                $clean[$cleanKey] = $this->sanitizeString((string)$val);
            }
        }

        return $clean;
    }

    /**
     * Sanitasi teks tunggal: hilangkan script, iframe, onload, dll.
     * Hanya izinkan teks bersih atau baris baru yang aman.
     */
    public function sanitizeString(?string $value): string
    {
        if ($value === null) {
            return '';
        }

        // Hapus tag HTML yang berpotensi XSS
        $stripped = strip_tags($value, '<p><br><b><strong><i><em><u><ul><ol><li>');

        // Hapus atribut jahat seperti javascript:, onerror, onload, dll
        $cleaned = preg_replace('/(javascript:|on\w+=)/i', '', $stripped);

        return trim($cleaned);
    }

    /**
     * Mendapatkan blok bawaan untuk Beranda Pesantren.
     */
    public function getDefaultBerandaBlocks(): array
    {
        return [
            [
                'id' => 'hero_1',
                'type' => 'hero',
                'data' => [
                    'badge' => 'Portal Resmi Lembaga Santri Putra',
                    'judul' => 'Pondok Pesantren Al-Falah Putra',
                    'subjudul' => 'Unit Lembaga Pendidikan Santri Putra di Bawah Naungan Pondok Pesantren Al-Falah Putak. Membina Generasi Berakhlak Mulia, Berilmu Luas, dan Berpegang Teguh pada Sunnah Nabi.',
                    'cta_text' => 'Lihat Warta Pengumuman',
                    'cta_url' => '/pengumuman',
                    'cta_secondary_text' => 'Verifikasi Keaslian Surat',
                    'cta_secondary_url' => '/verifikasi',
                ],
            ],
            [
                'id' => 'profil_1',
                'type' => 'profil',
                'data' => [
                    'judul' => 'Sekilas Tentang Pondok Pesantren Al-Falah Putra',
                    'kutipan' => 'Mendidik kader ulama dan insan mutafaqqih fiddin berakidah Ahlussunnah wal Jama\'ah an-Nahdliyyah bermadzhab Syafi\'i.',
                    'deskripsi' => 'Pondok Pesantren Al-Falah Putra merupakan unit lembaga pendidikan khusus santri putra di bawah naungan Pondok Pesantren Salafiyah Al-Falah Putak (Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan) yang didirikan oleh K.H. Mursyidi bersama Ny. Hj. Aniswatuzzuhriyah pada 5 Maret 2005 (10 Muharram). Al-Falah Putra memadukan sistem pendidikan Pesantren Salafiyah tradisional (pengkajian kitab kuning, Pondok Tahfidzul Qur\'an, Madrasah Diniyah Ula/Wustho/Ulya, dan Madrasah Qira\'ati) dengan pendidikan formal (MI, MTs, dan MA) serta pembinaan akhlakul karimah dan kemandirian santri.',
                    'visi' => 'Terwujudnya generasi santri yang bertakwa, berakhlakul karimah, menguasai ilmu agama (IMTAQ) dan ilmu pengetahuan umum (IPTEK), serta unggul dalam pembacaan kitab kuning dan hafalan Al-Qur\'an.',
                    'misi' => 'Menyelenggarakan pengkajian kitab-kitab salaf mu\'tabarah (sorogan, bandongan, halaqah, bahtsul masail), tahfidzul Qur\'an, serta pendidikan madrasah berjenjang yang disiplin, beradab, dan berjiwa khidmah.',
                ],
            ],
            [
                'id' => 'statistik_1',
                'type' => 'statistik',
                'data' => [
                    'judul' => 'Pondok Pesantren dalam Angka',
                    'subjudul' => 'Data riil perkembangan pendidikan santri Al-Falah Putra Putak',
                    'santri_aktif' => 'Otomatis Database',
                    'pengurus_asatidz' => 'Otomatis Database',
                    'asrama_kamar' => 'Otomatis Database',
                    'angkatan_alumni' => 'Otomatis Database',
                ],
            ],
            [
                'id' => 'program_1',
                'type' => 'program',
                'data' => [
                    'judul' => 'Program Pendidikan & Keilmuan',
                    'subjudul' => 'Sinergi Pesantren Salafiyah & Pendidikan Formal di Pondok Pesantren Al-Falah Putak',
                    'item_1_judul' => 'Kajian Kitab Kuning & Bahtsul Masail',
                    'item_1_desk' => 'Pengkajian kitab salaf mu\'tabarah dengan metode sorogan, bandongan, halaqah, hiwar, dan bahtsul masail di bawah bimbingan masyayikh dan asatidz.',
                    'item_2_judul' => 'Madrasah Diniyah & Pendidikan Formal (MI, MTs, MA)',
                    'item_2_desk' => 'Kurikulum terpadu Madrasah Diniyah (Ula, Wustho, Ulya), Madrasah Qira\'ati, serta jenjang formal MI, MTs, dan MA di lingkungan pesantren.',
                    'item_3_judul' => 'Pondok Tahfidzul Qur\'an & BLKK Informatika',
                    'item_3_desk' => 'Bimbingan menghafal Al-Qur\'an secara tartil dan mutqin, dilengkapi pengembangan keterampilan santri melalui Balai Latihan Kerja Komunitas (BLKK) Teknik Informatika.',
                ],
            ],
            [
                'id' => 'pengumuman_1',
                'type' => 'pengumuman',
                'data' => [
                    'judul' => 'Informasi & Pengumuman Resmi',
                    'subjudul' => 'Warta resmi sekretariat untuk santri, wali santri, dan masyarakat',
                    'limit' => '3',
                ],
            ],
            [
                'id' => 'kontak_1',
                'type' => 'kontak',
                'data' => [
                    'judul' => 'Sekretariat Pondok Pesantren Al-Falah Putra',
                    'subjudul' => 'Di bawah naungan Pondok Pesantren Al-Falah Putak — Layanan informasi, administrasi persuratan, dan kesantrian',
                    'alamat' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171',
                    'telepon' => '0812-7890-3117',
                    'email' => 'sekretariat@alfalahputra.sch.id',
                    'jam_kerja' => 'Sabtu - Kamis: 08:00 - 17:00 WIB (Jumat Libur)',
                ],
            ],
        ];
    }
}
