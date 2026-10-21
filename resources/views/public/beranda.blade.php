@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    @forelse($blocks as $block)
        @php
            $type = $block['type'] ?? '';
            $viewName = 'public.blocks.' . $type;
        @endphp

        @if(view()->exists($viewName))
            @include($viewName, [
                'block' => $block,
                'pengaturan' => $pengaturan,
                'pengumumanTerbaru' => $pengumumanTerbaru ?? collect(),
                'statistikOtomatis' => $statistikOtomatis ?? []
            ])
        @endif
    @empty
        <div class="py-24 text-center">
            <h1 class="text-3xl font-bold text-gray-800">Selamat Datang di Portal Pondok Pesantren Al-Falah Putra</h1>
            <p class="text-gray-500 mt-2">Halaman beranda sedang dalam persiapan.</p>
        </div>
    @endforelse
@endsection
