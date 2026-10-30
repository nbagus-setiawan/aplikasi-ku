{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
@php
    $appUrl = rtrim(config('app.url'), '/');
    // Jika masih localhost atau HTTP di server, arahkan paksa ke domain resmi HTTPS
    if (str_contains($appUrl, 'localhost') || !str_starts_with($appUrl, 'https://')) {
        $baseUrl = 'https://ponpesalfalahputak.my.id';
    } else {
        $baseUrl = $appUrl;
    }
    $halamanStatis = $halamanStatis ?? [];
    $lastmodBeranda = $lastmodBeranda ?? now()->toDateString();
    $lastmodPengumuman = $lastmodPengumuman ?? $lastmodBeranda;
@endphp
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
    <url>
        <loc>{{ $baseUrl }}/</loc>
        <lastmod>{{ $lastmodBeranda }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc>{{ $baseUrl }}/pengumuman</loc>
        <lastmod>{{ $lastmodPengumuman }}</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.9</priority>
    </url>
@foreach($halamanStatis as $path)
    <url>
        <loc>{{ $baseUrl }}{{ $path }}</loc>
        <lastmod>{{ $lastmodBeranda }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>{{ in_array($path, ['/profil', '/program-pendidikan', '/pendaftaran-santri', '/kontak']) ? '0.9' : '0.6' }}</priority>
    </url>
@endforeach
@foreach($pengumumans as $item)
    <url>
        <loc>{{ $baseUrl }}/pengumuman/{{ $item->slug }}</loc>
        <lastmod>{{ $item->updated_at->toDateString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.7</priority>
@if($item->gambar)
        <image:image>
            <image:loc>{{ $baseUrl }}/storage/{{ $item->gambar }}</image:loc>
        </image:image>
@endif
    </url>
@endforeach
</urlset>
