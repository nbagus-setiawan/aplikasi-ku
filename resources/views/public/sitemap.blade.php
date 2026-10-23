{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
@php
    $appUrl = rtrim(config('app.url'), '/');
    // Jika masih localhost atau HTTP di server, arahkan paksa ke domain resmi HTTPS
    if (str_contains($appUrl, 'localhost') || !str_starts_with($appUrl, 'https://')) {
        $baseUrl = 'https://ponpesalfalahputak.my.id';
    } else {
        $baseUrl = $appUrl;
    }
    // Jika host saat ini adalah ponpesalfalahputak.my.id
    if (request()->getHost() === 'ponpesalfalahputak.my.id') {
        $baseUrl = 'https://ponpesalfalahputak.my.id';
    }
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <!-- Halaman Beranda Utama -->
    <url>
        <loc>{{ $baseUrl }}/</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Halaman Warta & Pengumuman Resmi -->
    <url>
        <loc>{{ $baseUrl }}/pengumuman</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Halaman Layanan Konsultasi -->
    <url>
        <loc>{{ $baseUrl }}/konsultasi</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Halaman Verifikasi Dokumen Sah -->
    <url>
        <loc>{{ $baseUrl }}/verifikasi</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Seluruh Warta / Pengumuman Publik -->
    @foreach($pengumumans as $item)
        <url>
            <loc>{{ $baseUrl }}/pengumuman/{{ $item->slug }}</loc>
            <lastmod>{{ $item->updated_at ? $item->updated_at->toDateString() : now()->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
            @if($item->gambar)
                <image:image>
                    <image:loc>{{ $baseUrl }}/storage/{{ $item->gambar }}</image:loc>
                    <image:title>{{ htmlspecialchars($item->judul, ENT_XML1, 'UTF-8') }}</image:title>
                </image:image>
            @endif
        </url>
    @endforeach
</urlset>
