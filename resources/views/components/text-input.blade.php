@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-emerald-custom dark:focus:border-emerald-custom focus:ring-emerald-custom dark:focus:ring-emerald-custom rounded-md shadow-sm']) }}>
