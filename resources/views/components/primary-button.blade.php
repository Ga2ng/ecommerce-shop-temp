<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-black dark:bg-emerald-custom border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-900 dark:hover:bg-[#0ea572] focus:bg-gray-900 dark:focus:bg-[#0ea572] active:bg-gray-800 dark:active:bg-[#0d9466] focus:outline-none focus:ring-2 focus:ring-emerald-custom focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
