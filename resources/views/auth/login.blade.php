<x-guest-layout>
    <div class="mb-5 text-center">
        <h2 class="text-xl font-bold text-gray-900">Masuk Panel Sekretariat</h2>
        <p class="text-xs text-gray-500 mt-1">Silakan masukkan email dan kata sandi akun sekretaris.</p>
    </div>

    <!-- Status Sesi -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Alamat Email -->
        <div>
            <label for="email" class="block text-xs font-semibold uppercase text-gray-700 mb-1">Alamat Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                   placeholder="sekretaris@alfalahputra.sch.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Kata Sandi -->
        <div>
            <label for="password" class="block text-xs font-semibold uppercase text-gray-700 mb-1">Kata Sandi</label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm"
                   placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Ingat Saya & Lupa Sandi -->
        <div class="flex items-center justify-between text-xs pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                       class="rounded border-gray-300 text-emerald-600 shadow-sm focus:ring-emerald-500">
                <span class="ms-2 text-gray-600">Ingat Saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-emerald-700 hover:text-emerald-900 hover:underline font-medium" href="{{ route('password.request') }}">
                    Lupa kata sandi?
                </a>
            @endif
        </div>

        <div class="pt-2">
            <button type="submit"
                    class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-sm rounded-lg shadow transition focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
                Masuk ke Panel
            </button>
        </div>
    </form>

    <div class="mt-6 pt-4 border-t border-gray-100 text-center">
        <p class="text-[11px] text-gray-400">
            Hanya untuk Sekretaris Pondok Pesantren Al Falah Putra. Pendaftaran akun publik dinonaktifkan.
        </p>
    </div>
</x-guest-layout>
