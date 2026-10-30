{{-- Ajakan ke halaman lain: memperkuat tautan internal antarhalaman (PRD 6.12) --}}
@php
    $tautanLain = collect([
        ['url' => '/profil', 'judul' => 'Profil Pondok', 'desk' => 'Sejarah, visi, misi, dan pengasuh pondok pesantren.'],
        ['url' => '/program-pendidikan', 'judul' => 'Program Pendidikan', 'desk' => 'Kitab kuning, tahfidz, madrasah diniyah, dan pendidikan formal.'],
        ['url' => '/pendaftaran-santri', 'judul' => 'Pendaftaran Santri Baru', 'desk' => 'Alur dan informasi penerimaan santri putra dan putri.'],
        ['url' => '/kontak', 'judul' => 'Kontak & Lokasi', 'desk' => 'Alamat, WhatsApp sekretariat, dan peta lokasi pondok.'],
    ])->reject(fn ($t) => request()->is(ltrim($t['url'], '/')));
@endphp
<section class="py-10 sm:py-14 bg-slate-50 border-t border-slate-200/80">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-8 space-y-1.5">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold text-emerald-800 bg-emerald-100/70 uppercase tracking-wider">
                Eksplorasi Halaman
            </span>
            <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight">
                Informasi Lain Seputar Pondok Pesantren
            </h2>
            <p class="text-xs sm:text-sm text-slate-500">
                Pilih menu di bawah untuk melihat rincian informasi dan profil resmi lainnya.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($tautanLain as $t)
                <a href="{{ url($t['url']) }}"
                   class="group block bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 hover:shadow-xs transition duration-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm sm:text-base font-bold text-slate-900 group-hover:text-emerald-800 transition">
                            {{ $t['judul'] }}
                        </span>
                        <span class="w-6 h-6 rounded-full bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs group-hover:bg-emerald-600 group-hover:text-white transition">
                            &rarr;
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        {{ $t['desk'] }}
                    </p>
                </a>
            @endforeach
        </div>
    </div>
</section>

