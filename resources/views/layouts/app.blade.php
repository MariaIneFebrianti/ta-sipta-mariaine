<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navbar')
            @include('layouts.sidebar')

            <!-- Page Heading -->
            {{-- @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset --}}

            <!-- Page Content -->
            <main class="sm:ml-[20px] sm:mt-[60px]">
                @yield('content')
            </main>
        </div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("#datepicker-autohide", {
            dateFormat: "Y-m-d", // Sesuaikan format tanggal sesuai kebutuhan
            allowInput: true, // Memungkinkan pengguna untuk mengetik tanggal
        });
    });
</script>

<script>
    // Fokus pada input pencarian dan pindahkan kursor ke akhir saat halaman dimuat
    window.onload = function() {
        const searchInput = document.getElementById('search-input');
        searchInput.focus(); // Fokus pada input
        searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length); // Pindahkan kursor ke akhir
    };
</script>
    </body>
</html>
