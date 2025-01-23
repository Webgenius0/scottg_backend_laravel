<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <style>
            /* Fallback styles if Vite is unavailable */
            .placeholder-image {
                background-color: #f3f4f6;
                height: 200px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.5rem;
                color: #9ca3af;
                text-transform: uppercase;
                font-weight: bold;
                border-radius: 0.5rem;
            }

            .icon {
                font-size: 1.25rem;
                margin-right: 0.5rem;
            }
        </style>
    @endif
</head>

<body class="bg-gray-100 text-gray-800">

    <!-- Navigation -->
    <nav class="container mx-auto py-4 px-6 flex items-center justify-center">
        {{-- <a href="{{ url('/') }}" class="text-3xl font-bold flex items-center">
            <i class="fas fa-code icon text-red-500"></i> Scottg
        </a> --}}
        <div class="flex items-center space-x-6">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/admin/dashboard') }}"
                        class="rounded-md px-3 py-2 transition text-black hover:text-gray-700 focus:ring-2 focus:ring-[#FF2D20]">
                        <i class="fas fa-tachometer-alt icon"></i> Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="rounded-md px-3 py-2 transition text-black hover:text-gray-700 focus:ring-2 focus:ring-[#FF2D20]">
                        <i class="fas fa-sign-in-alt icon"></i> Log in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                            class="rounded-md px-3 py-2 transition text-black hover:text-gray-700 focus:ring-2 focus:ring-[#FF2D20]">
                            <i class="fas fa-user-plus icon"></i> Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="container mx-auto py-12 px-6 text-center">
        <div class="flex flex-col items-center space-y-6">
            <div class="placeholder-image">
                <i class="fas fa-laravel text-red-500"></i>
            </div>
            <h1 class="text-4xl font-bold">Welcome to Scottg!</h1>
            <p class="mt-4 text-gray-600">
                Your application is up and running. Customize it to suit your needs.
            </p>
        </div>
    </header>

    <!-- Features Section -->
    <section class="container mx-auto py-12 px-6">
        <h2 class="text-3xl font-bold text-center mb-8">Features</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <i class="fas fa-shield-alt text-red-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Secure</h3>
                <p class="text-gray-600">Built with modern security standards to keep your data safe.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <i class="fas fa-bolt text-yellow-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Fast</h3>
                <p class="text-gray-600">Optimized for high performance and speed.</p>
            </div>
            <div class="bg-white shadow-md rounded-lg p-6 text-center">
                <i class="fas fa-cogs text-blue-500 text-4xl mb-4"></i>
                <h3 class="text-xl font-semibold mb-2">Customizable</h3>
                <p class="text-gray-600">Easily adapt the application to fit your specific needs.</p>
            </div>
        </div>
    </section>

</body>

</html>
