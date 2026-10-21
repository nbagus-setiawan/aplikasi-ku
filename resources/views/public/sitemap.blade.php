{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Halaman Beranda Utama -->
    <url>
        <loc>{{ url('/') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>

    <!-- Halaman Warta & Pengumuman Resmi -->
    <url>
        <loc>{{ url('/pengumuman') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>

    <!-- Halaman Verifikasi Dokumen Sah -->
    <url>
        <loc>{{ url('/verifikasi') }}</loc>
        <lastmod>{{ now()->toDateString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>

    <!-- Seluruh Warta / Pengumuman Publik -->
    @foreach($pengumumans as $item)
        <url>
            <loc>{{ url('/pengumuman/' . $item->slug) }}</loc>
            <lastmod>{{ $item->updated_at ? $item->updated_at->toDateString() : now()->toDateString() }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach
</urlset>
