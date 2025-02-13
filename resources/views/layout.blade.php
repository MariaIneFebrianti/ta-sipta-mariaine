<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
{{-- <body class="bg-gray-100">
    @include('layouts.sidebar')
    <div class="overlay" id="overlay"></div>

    @include('layouts.navbar')

    <main class="container mx-auto mt-4">
        @yield('content')
    </main>

    @include('layouts.footer')

    @vite('resources/js/app.js')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session()->has('alert.title') && session()->has('alert.text') && session()->has('alert.icon'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('alert.title') }}',
                    text: '{{ session('alert.text') }}',
                    icon: '{{ session('alert.icon') }}'
                });
            });
        </script>
    @endif
</body> --}}
{{-- <body class="bg-gray-100 flex flex-col min-h-screen">
    @include('layouts.sidebar')
    <div class="overlay" id="overlay"></div>

    @include('layouts.navbar')

    <!-- Tambahkan flex-1 agar main mengambil sisa tinggi layar -->
    <main class="container mx-auto mt-4 flex-1">
        @yield('content')
    </main>

    <!-- Footer akan tetap di bawah -->
    @include('layouts.footer')

    @vite('resources/js/app.js')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session()->has('alert.title') && session()->has('alert.text') && session()->has('alert.icon'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('alert.title') }}',
                    text: '{{ session('alert.text') }}',
                    icon: '{{ session('alert.icon') }}'
                });
            });
        </script>
    @endif
</body> --}}

<body class="bg-gray-100 flex flex-col min-h-screen">

    @include('layouts.sidebar')
    <div class="overlay" id="overlay"></div>

    @include('layouts.navbar')

    @include('layouts.breadcrumb')

    <!-- Tambahkan flex-1 agar main mengambil sisa tinggi layar -->
    <main class="container mx-auto mt-4 flex-1">
        @yield('content')
    </main>

    <!-- Footer akan tetap di bawah -->
    @include('layouts.footer')

    @vite('resources/js/app.js')
    @stack('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @if (session()->has('alert.title') && session()->has('alert.text') && session()->has('alert.icon'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('alert.title') }}',
                    text: '{{ session('alert.text') }}',
                    icon: '{{ session('alert.icon') }}'
                });
            });
        </script>
    @endif
    </body>

</html>

<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.8.1/flowbite.min.js"></script>

{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll("[data-collapse-toggle]").forEach(function(button) {
            button.addEventListener("click", function() {
                let targetId = this.getAttribute("data-collapse-toggle");
                let targetMenu = document.getElementById(targetId);

                if (targetMenu.classList.contains("hidden")) {
                    targetMenu.classList.remove("hidden");
                } else {
                    targetMenu.classList.add("hidden");
                }
            });
        });
    });
</script> --}}
{{-- <script>
    document.addEventListener("DOMContentLoaded", function () {
        const userMenuButton = document.getElementById("user-menu-button");
        const userDropdown = document.getElementById("user-dropdown");

        userMenuButton.addEventListener("click", function () {
            userDropdown.classList.toggle("hidden");
        });

        // Menutup dropdown jika klik di luar area dropdown
        document.addEventListener("click", function (event) {
            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add("hidden");
            }
        });
    });
  </script> --}}
