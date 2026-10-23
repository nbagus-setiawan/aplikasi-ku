@extends('layouts.public')

@section('title', 'Layanan Konsultasi & Informasi Santri')

@section('content')
<!-- Header Halaman Konsultasi -->
<section class="bg-emerald-950 text-white py-10 sm:py-16 border-b border-emerald-900/60">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 text-xs font-semibold uppercase tracking-wider">
            Pusat Informasi &amp; Layanan Santri
        </span>
        <h1 class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight">
            Layanan Konsultasi Pondok Pesantren
        </h1>
        <p class="max-w-xl mx-auto text-emerald-100/90 text-xs sm:text-sm md:text-base font-normal leading-relaxed">
            Ahlan wa Sahlan. Sampaikan pertanyaan atau keperluan konsultasi seputar santri, pendidikan, dan kepengurusan langsung ke WhatsApp sekretariat pondok pesantren.
        </p>
    </div>
</section>

<!-- Konten Utama: Formulir & Informasi Layanan -->
<section class="py-10 sm:py-14 bg-slate-50 min-h-[550px]">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start"
             x-data="{
                nama: '',
                asal: '',
                kontak: '',
                topik: '',
                pesan: '',
                errorMsg: '',
                kirimKonsultasi() {
                    this.errorMsg = '';
                    if (!this.nama.trim()) {
                        this.errorMsg = 'Silakan isi Nama Lengkap Anda terlebih dahulu.';
                        return;
                    }
                    if (!this.asal.trim()) {
                        this.errorMsg = 'Silakan isi Asal Daerah / Kota Anda.';
                        return;
                    }
                    if (!this.topik.trim()) {
                        this.errorMsg = 'Silakan ketik Topik Konsultasi Anda.';
                        return;
                    }
                    if (!this.pesan.trim()) {
                        this.errorMsg = 'Silakan tulis Pertanyaan / Pesan Konsultasi Anda.';
                        return;
                    }

                    const nomorWa = '{{ $nomorWaClean }}';
                    let text = 'Assalamu\'alaikum Warahmatullahi Wabarakatuh.\n\n';
                    text += 'Kepada Yth. Pengurus Sekretariat Pondok Pesantren Salafiyah Al-Falah Putak,\n\n';
                    text += 'Perkenalkan, saya ingin berkonsultasi mengenai informasi pesantren:\n\n';
                    text += '• *Nama Lengkap:* ' + this.nama.trim() + '\n';
                    text += '• *Asal Daerah / Kota:* ' + this.asal.trim() + '\n';
                    if (this.kontak.trim()) {
                        text += '• *No. WhatsApp / Kontak:* ' + this.kontak.trim() + '\n';
                    }
                    text += '• *Topik Konsultasi:* ' + this.topik.trim() + '\n\n';
                    text += '• *Pesan / Pertanyaan:*\n' + this.pesan.trim() + '\n\n';
                    text += 'Mohon arahan dan petunjuk dari Bapak/Ibu Pengurus. Terima kasih.\n';
                    text += 'Jazakumullahu Khairan Katsiran.\n';
                    text += 'Wassalamu\'alaikum Warahmatullahi Wabarakatuh.';

                    const waUrl = 'https://api.whatsapp.com/send?phone=' + nomorWa + '&text=' + encodeURIComponent(text);
                    window.open(waUrl, '_blank');
                }
             }">

            <!-- KOLOM KIRI: FORMULIR KONSULTASI -->
            <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/90 shadow-xs space-y-5">
                <div class="border-b border-slate-100 pb-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-900 font-bold text-xs uppercase tracking-wider mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        <span>Formulir Konsultasi</span>
                    </div>
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                        Isi Data Konsultasi
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 leading-relaxed">
                        Lengkapi formulir di bawah ini dengan santun, sistem akan menyusun pesan rapi langsung ke kontak WhatsApp pengurus.
                    </p>
                </div>

                <!-- Pesan Peringatan Validasi Form -->
                <div x-show="errorMsg" x-cloak class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 shrink-0 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="errorMsg"></span>
                </div>

                <form @submit.prevent="kirimKonsultasi()" class="space-y-4">
                    <!-- 1. Nama Lengkap -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700">
                            Nama Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               x-model="nama"
                               placeholder="Contoh: Muhammad Ihsan / H. Sulaiman"
                               required
                               class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 min-h-[46px] placeholder:text-slate-400">
                    </div>

                    <!-- 2. Asal Daerah / Kota -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700">
                            Asal Daerah / Kota <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               x-model="asal"
                               placeholder="Contoh: Palembang, Muara Enim, Prabumulih"
                               required
                               class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 min-h-[46px] placeholder:text-slate-400">
                    </div>

                    <!-- 3. No. WhatsApp / Kontak -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700">
                            Nomor WhatsApp / Kontak <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="tel"
                               x-model="kontak"
                               placeholder="Contoh: 0812-3456-7890"
                               class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 min-h-[46px] placeholder:text-slate-400 font-mono">
                    </div>

                    <!-- 4. Topik Konsultasi (Mengetik Sendiri Sesuai Arahan Pengguna) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700">
                            Topik Konsultasi <span class="text-rose-500">*</span>
                        </label>
                        <input type="text"
                               x-model="topik"
                               placeholder="Ketik topik Anda, misal: Pendaftaran Santri Baru / Pindah Sekolah / Ijazah"
                               required
                               class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 min-h-[46px] placeholder:text-slate-400">
                        <p class="text-[11px] text-slate-400 leading-tight">
                            Ketik keperluan spesifik yang ingin Anda konsultasikan secara bebas.
                        </p>
                    </div>

                    <!-- 5. Pesan / Pertanyaan Konsultasi -->
                    <div class="space-y-1.5">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700">
                            Pertanyaan / Pesan Konsultasi <span class="text-rose-500">*</span>
                        </label>
                        <textarea x-model="pesan"
                                  rows="4"
                                  placeholder="Tuliskan secara ringkas dan sopan pertanyaan atau hal yang ingin Anda konsultasikan dengan pengurus..."
                                  required
                                  class="w-full rounded-xl border border-slate-200 text-sm sm:text-base focus:border-emerald-500 focus:ring-emerald-500 py-3 px-4 placeholder:text-slate-400 leading-relaxed"></textarea>
                    </div>

                    <!-- Tombol CTA Utama ke WA -->
                    <div class="pt-2">
                        <button type="submit"
                                style="background-color: #25D366;"
                                class="w-full inline-flex items-center justify-center gap-3 min-h-[50px] px-6 py-3.5 rounded-xl font-bold text-sm sm:text-base text-white shadow-md shadow-emerald-600/20 hover:opacity-95 active:scale-95 transition text-center cursor-pointer">
                            <!-- Ikon WhatsApp Asli -->
                            <svg class="w-6 h-6 shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            <span>Konsultasikan Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- KOLOM KANAN: INFORMASI LAYANAN & ETIKA -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Kartu Jam Pelayanan Resmi -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/90 shadow-xs space-y-4">
                    <div class="flex items-center gap-3 border-b border-slate-100 pb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-slate-900">Jadwal Jam Layanan</h3>
                            <p class="text-xs text-slate-500">Waktu pelayanan resmi sekretariat</p>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div class="flex items-start justify-between gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <span class="font-bold text-slate-900 block">Senin - Kamis</span>
                                <span class="text-[11px] text-slate-500">Pelayanan Aktif</span>
                            </div>
                            <span class="font-mono font-bold text-emerald-800 bg-emerald-100/70 px-2.5 py-1 rounded-lg text-xs">
                                08.00 - 16.00 WIB
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-2 p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div>
                                <span class="font-bold text-slate-900 block">Sabtu - Minggu</span>
                                <span class="text-[11px] text-slate-500">Pelayanan Akhir Pekan</span>
                            </div>
                            <span class="font-mono font-bold text-emerald-800 bg-emerald-100/70 px-2.5 py-1 rounded-lg text-xs">
                                07.30 - 15.00 WIB
                            </span>
                        </div>

                        <div class="flex items-start justify-between gap-2 p-3 rounded-xl bg-rose-50/60 border border-rose-100">
                            <div>
                                <span class="font-bold text-rose-900 block">Hari Jumat</span>
                                <span class="text-[11px] text-rose-600">Kegiatan Ibadah &amp; Pembinaan Santri</span>
                            </div>
                            <span class="font-bold text-rose-700 text-xs">
                                Libur Pelayanan
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kartu Kontak Resmi & Lokasi -->
                <div class="bg-emerald-950 text-white p-6 sm:p-7 rounded-3xl shadow-sm space-y-4 border border-emerald-900">
                    <div class="flex items-center gap-2.5">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok" class="w-9 h-9 object-contain">
                        <div>
                            <span class="block text-xs font-bold uppercase tracking-wider text-emerald-300">Sekretariat Resmi</span>
                            <span class="block text-sm sm:text-base font-extrabold text-white">PP Salafiyah Al-Falah Putak</span>
                        </div>
                    </div>

                    <div class="pt-2 space-y-2.5 text-xs sm:text-sm text-slate-200">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span class="font-mono font-bold text-white">{{ $nomorWa }}</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                            <span class="text-slate-300">{{ $pengaturan['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}</span>
                        </div>
                    </div>

                    <!-- Panduan Etika Bertanya -->
                    <div class="pt-3 border-t border-emerald-900/80 text-[11px] sm:text-xs text-emerald-200/90 leading-relaxed space-y-1">
                        <span class="font-bold text-white block">Adab &amp; Etika Konsultasi:</span>
                        <p>&bull; Mengawali dengan salam yang santun (Assalamu'alaikum).</p>
                        <p>&bull; Menyampaikan nama dan pertanyaan secara jelas.</p>
                        <p>&bull; Pengurus akan membalas pesan sesuai antrean pada jam operasional.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
