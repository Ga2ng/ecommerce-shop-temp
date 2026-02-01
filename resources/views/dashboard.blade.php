<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h3 class="text-2xl font-bold mb-2">Welcome, {{ auth()->user()->name }}!</h3>
                        <p class="text-gray-600 dark:text-gray-400">You're successfully logged in.</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                        <a href="{{ route('catalog.index') }}" class="block p-6 bg-gradient-to-br from-emerald-custom to-[#0ea572] text-white rounded-lg hover:shadow-lg transition-shadow">
                            <h4 class="text-xl font-bold mb-2">Browse Products</h4>
                            <p class="text-emerald-50">Explore our product catalog</p>
                        </a>
                        
                        <a href="/#news" class="block p-6 bg-gradient-to-br from-gray-900 to-black text-white rounded-lg hover:shadow-lg transition-shadow">
                            <h4 class="text-xl font-bold mb-2">Latest News</h4>
                            <p class="text-gray-300">Stay updated with our news</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
