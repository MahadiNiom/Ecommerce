<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Matir Shaad'))</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&family=Fraunces:opsz,wght@9..144,400..700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col">
    @include('components.navbar')

    <main class="flex-1">
        <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8 sm:py-12">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    <div class="toast">
        @if (session('success'))
            <div class="toast-body text-emerald-800 border-emerald-300 bg-emerald-50"
                 x-data="{ visible: false }"
                 x-init="visible = true; setTimeout(() => { $root.classList.add('animate-slide-out-right'); setTimeout(() => $root.remove(), 300) }, 4500)"
                 role="status">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-emerald-500 text-sm font-bold text-white">&#10003;</span>
                <span class="text-emerald-900">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-body text-red-700 border-red-300 bg-red-50"
                 x-data="{ visible: false }"
                 x-init="visible = true; setTimeout(() => { $root.classList.add('animate-slide-out-right'); setTimeout(() => $root.remove(), 300) }, 4500)"
                 role="alert">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-red-500 text-sm font-bold text-white">&#10005;</span>
                <span class="text-red-800">{{ session('error') }}</span>
            </div>
        @endif
    </div>
</body>
</html>