<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900">
            Informasi Profil
        </h2>

        <p class="mt-1 text-sm text-gray-500">
            Perbarui nama pengguna dan alamat surel (email) resmi akun sekretariat pondok pesantren.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama Lengkap" class="text-xs uppercase font-bold text-gray-700" />
            <input id="name" name="name" type="text"
                   class="mt-1 block w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs"
                   value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Alamat Email" class="text-xs uppercase font-bold text-gray-700" />
            <input id="email" name="email" type="email"
                   class="mt-1 block w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs"
                   value="{{ old('email', $user->email) }}" required autocomplete="username" />
            <x-input-error class="mt-1" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 text-sm text-gray-700">
                    <p>
                        Alamat email belum diverifikasi.
                        <button form="send-verification" class="underline text-sm text-emerald-700 hover:text-emerald-800 font-medium focus:outline-none">
                            Klik di sini untuk mengirim ulang tautan verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-emerald-600">
                            Tautan verifikasi baru telah dikirim ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-sm rounded-xl shadow-md shadow-emerald-700/20 transition-all hover:scale-[1.01] active:scale-[0.99]">
                Simpan Profil
            </button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-bold text-emerald-700"
                >Perubahan profil berhasil disimpan.</p>
            @endif
        </div>
    </form>
</section>
