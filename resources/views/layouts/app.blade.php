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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@700&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {

    const formSimpan = document.getElementById('formSimpan');

    if (formSimpan) {
        formSimpan.addEventListener('submit', function () {
            document.getElementById('loadingPopup').classList.remove('d-none');
        });
    }

    function showPopup(title, message, type) {

        const iconMap = {
            success: "✅",
            error: "❌"
        };

        document.getElementById('popupIconWrap').innerHTML = iconMap[type] || "❌";
        document.getElementById('popupTitle').innerText = title;
        document.getElementById('popupMessage').innerText = message;

        document.getElementById('loadingPopup').classList.add('d-none');
        document.getElementById('resultPopup').classList.remove('d-none');

        setTimeout(() => closePopup(), 3000);
    }

    window.closePopup = function () {
        document.getElementById('resultPopup').classList.add('d-none');
    };

    @if(session('success'))
        showPopup(
            "Berhasil",
            "{{ session('success') }}",
            "success"
        );
    @endif

    @if(session('error'))
        showPopup(
            "Terjadi Kesalahan",
            "{{ session('error') }}",
            "error"
        );
    @endif

    @if(session('warning'))
    showPopup(
        "Mode AI Nonaktif",
        "{{ session('warning') }}",
        "error"
    );
    @endif  
});
</script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    {{-- LOADING POPUP --}}
<div id="loadingPopup"
     class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
     style="z-index:1060">

    <div class="popup-box bg-white p-5 text-center" style="width:340px">
        <div class="spinner-border text-primary mb-3"></div>
        <p class="fw-semibold mb-1">Menganalisis Data…</p>
        <p class="text-muted small mb-0">
            Mohon tunggu beberapa saat
        </p>
    </div>
</div>

{{-- RESULT POPUP --}}
<div id="resultPopup"
     class="popup-overlay position-fixed top-0 start-0 w-100 h-100 d-none d-flex align-items-center justify-content-center"
     style="z-index:1060">

    <div class="popup-box bg-white p-4 text-center" style="width:360px">

        <div id="popupIconWrap" class="mb-3"></div>

        <h5 id="popupTitle" class="fw-semibold mb-2"></h5>
        <p id="popupMessage" class="text-muted small mb-3"></p>

        <button onclick="closePopup()" class="btn btn-primary">
            Tutup
        </button>

    </div>
</div>
</body>

</html>
