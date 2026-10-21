@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Layanan Pondok';
    $subjudul = $data['subjudul'] ?? 'Pusat informasi, pelayanan persuratan santri, dan verifikasi dokumen resmi Pondok Pesantren Al-Falah Putak';
    $subjudul = str_replace('Al-Falah Putra', 'Al-Falah Putak', $subjudul);
    $alamat = $data['alamat'] ?? ($pengaturan['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171');
    $telepon = $data['telepon'] ?? ($pengaturan['kontak'] ?? '0821-1870-2782');
    $email = $data['email'] ?? 'pengurusalfalahputra@gmail.com';
    $jamKerja = $data['jam_kerja'] ?? 'Sabtu - Kamis: 08:00 - 17:00 WIB ';
@endphp

<section class="py-10 sm:py-14 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10 space-y-2">
            <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">
                Kontak & Pelayanan
            </span>
            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                {{ $judul }}
            </h2>
            <p class="text-slate-600 text-xs sm:text-sm md:text-base leading-relaxed">
                {{ $subjudul }}
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
            <!-- 1. Alamat -->
            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full hover:border-emerald-300 hover:bg-white hover:shadow-sm transition duration-200">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Alamat Pondok</h3>
                    <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed">{{ $alamat }}</p>
                </div>
                <span class="pt-3.5 mt-3.5 border-t border-slate-200/60 text-xs font-semibold text-emerald-800">Pondok Pesantren Al-Falah Putak</span>
            </div>

            <!-- 2. Telepon -->
            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full hover:border-emerald-300 hover:bg-white hover:shadow-sm transition duration-200">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Telepon Sekretariat</h3>
                    <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed">{{ $telepon }}</p>
                </div>
                <span class="pt-3.5 mt-3.5 border-t border-slate-200/60 text-xs font-semibold text-emerald-800">WhatsApp &amp; Telepon</span>
            </div>

            <!-- 3. Email -->
            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full hover:border-emerald-300 hover:bg-white hover:shadow-sm transition duration-200">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Email Resmi</h3>
                    <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed break-all">{{ $email }}</p>
                </div>
                <span class="pt-3.5 mt-3.5 border-t border-slate-200/60 text-xs font-semibold text-emerald-800">Persuratan Resmi</span>
            </div>

            <!-- 4. Jam Kerja -->
            <div class="bg-slate-50/80 p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between h-full hover:border-emerald-300 hover:bg-white hover:shadow-sm transition duration-200">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-1">Jam Pelayanan</h3>
                    <p class="text-xs sm:text-sm font-semibold text-slate-900 leading-relaxed">{{ $jamKerja }}</p>
                </div>
                <span class="pt-3.5 mt-3.5 border-t border-slate-200/60 text-xs font-semibold text-emerald-800">Waktu Indonesia Barat</span>
            </div>
        </div>
    </div>
</section>
