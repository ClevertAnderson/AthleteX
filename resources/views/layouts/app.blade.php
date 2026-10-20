<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'AthleteX')</title>

    <!-- Prevent browser from serving stale cached auth sessions -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <!-- Tailwind styling -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Bootstrap Icons -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Force Ma'am Daph's Official SDO Green Globally -->
    <style>
        .bg-green-500, .bg-green-600, .bg-green-700, .bg-green-800 { background-color: #16592D !important; }
        .text-green-500, .text-green-600, .text-green-700, .text-green-800 { color: #16592D !important; }
        .border-green-500, .border-green-600, .border-green-700, .border-green-800 { border-color: #16592D !important; }
        .hover\:bg-green-600:hover { background-color: #104221 !important; }

        /* 1. Prevent the entire page from breaking horizontally */
        html, body {
            overflow-x: hidden;
            width: 100%;
        }

        /* 2. Instantly make ALL tables scrollable on mobile */
        @media (max-width: 768px) {
            table {
                display: block !important;
                overflow-x: auto !important;
                white-space: nowrap !important;
                width: 100% !important;
                -webkit-overflow-scrolling: touch; /* Smooth swiping on iOS */
            }
        }
    </style>

    <style>
    body::after {
        content: "";
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        
        width: 95vw;
        height: 95vw;
        max-width: 850px; 
        max-height: 850px;
        
        background-image: url('{{ asset("images/jaguars.png") }}');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 50%;
        
        opacity: 0.06;
        z-index: 0; /* Changed from 9999 to 0 so it stays behind touchable UI */
        pointer-events: none !important; 
    }
    </style>
</head>

<body class="bg-gray-50">
    
    <!-- Mobile Header -->
    <header class="md:hidden bg-[#2e4e1f] text-white p-4 flex flex-row justify-between items-center shadow-md fixed top-0 w-full z-40">
        <span class="font-bold text-lg">AthleteX</span>
        <!-- Hamburger Button -->
        <button id="mobileMenuBtn" class="text-white text-2xl focus:outline-none cursor-pointer">
            <i class="bi bi-list"></i>
        </button>
    </header>

    <!-- Backdrop overlay for mobile sidebar -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

    <!-- The Sidebar -->
    @include('partials.sidebar')

    <!-- Main Content -->
    <div class="md:pl-64 flex flex-col min-h-screen pt-16 md:pt-0 relative z-10">
        <main class="flex-1 w-full p-4 md:p-8 bg-gray-50">
            @yield('content')
        </main>
    </div>

    <!-- Sidebar Toggle & Backdrop Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const sidebar = document.getElementById('sidebar-wrapper');
            const backdrop = document.getElementById('sidebarBackdrop');

            if (mobileMenuBtn && sidebar) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('-translate-x-full');
                    backdrop?.classList.toggle('hidden');
                });

                backdrop?.addEventListener('click', function() {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('hidden');
                });
            }
        });
    </script>
</body>
</html>