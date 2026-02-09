<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'E-comm') }}</title>

        @include('layouts.partials.theme-init')

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            <!-- Logo -->
            <div class="mb-8">
                <a href="/" class="flex items-center space-x-3 group">
                    <div class="w-16 h-16 bg-emerald-custom rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform shadow-lg">
                        <span class="text-white font-bold text-2xl">E</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-black dark:text-white">E-comm</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">E-commerce Platform</p>
                    </div>
                </a>
            </div>

            <!-- Form Card -->
            <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl">
                {{ $slot }}
            </div>

            <!-- Footer Link -->
            <div class="mt-8 text-center">
                <a href="/" class="text-sm text-gray-600 dark:text-gray-400 hover:text-emerald-custom transition-colors">
                    ← Back to Home
                </a>
            </div>
        </div>
    </body>
</html>
