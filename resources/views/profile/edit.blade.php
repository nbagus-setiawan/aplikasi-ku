<x-app-layout>
    @section('title', 'Profil Sekretaris')

    <div class="space-y-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
            <a href="{{ route('dashboard') }}" class="hover:text-emerald-700 transition">Dashboard</a>
            <span>/</span>
            <span class="text-gray-800 font-bold">Profil Sekretaris</span>
        </nav>

        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Profil Sekretaris</h1>
            <p class="text-sm text-gray-500 mt-1">Perbarui nama, alamat email resmi, dan kata sandi akun sekretariat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 shadow-sm rounded-xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
