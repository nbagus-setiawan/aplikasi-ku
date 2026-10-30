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
        <h2 class="text-lg sm:text-2xl font-extrabold text-slate-900 tracking-tight text-center mb-6 sm:mb-8">
            Informasi Lain Seputar Pondok Pesantren Al-Falah Putak
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($tautanLain as $t)
                <a href="{{ url($t['url']) }}" class="block bg-white p-5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 hover:shadow-sm transition">
                    <span class="block text-sm sm:text-base font-bold text-emerald-900">{{ $t['judul'] }} &rarr;</span>
                    <span class="block text-xs sm:text-sm text-slate-600 mt-1 leading-relaxed">{{ $t['desk'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

