<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'AthleteX')</title>
    
    {{-- Vite CSS - Compiled Tailwind + Custom Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Local Bootstrap CSS (Offline) --}}
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    {{-- Force Ma'am Daph's Official SDO Green Globally --}}
    <style>
        .bg-green-500, .bg-green-600, .bg-green-700, .bg-green-800 { background-color: #16592D !important; }
        .text-green-500, .text-green-600, .text-green-700, .text-green-800 { color: #16592D !important; }
        .border-green-500, .border-green-600, .border-green-700, .border-green-800 { border-color: #16592D !important; }
        
        /* Hover States (Slightly darker shade for a natural click effect) */
        .hover\:bg-green-500:hover, .hover\:bg-green-600:hover, .hover\:bg-green-700:hover { background-color: #104221 !important; }
        .hover\:text-green-500:hover, .hover\:text-green-600:hover, .hover\:text-green-700:hover { color: #104221 !important; }
        
        /* Focus Rings */
        .focus\:ring-green-400:focus, .focus\:ring-green-500:focus, .focus\:ring-green-600:focus { box-shadow: 0 0 0 2px #16592D !important; }
    </style>
</head>
<body class="bg-gray-50">

    <div class="flex min-h-screen">
        
        @include('partials.sidebar')

        <div class="flex-1 ml-64">
            @yield('content')
        </div>

    </div>

    {{-- Local Bootstrap JS (Offline) --}}
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

</body>
</html>