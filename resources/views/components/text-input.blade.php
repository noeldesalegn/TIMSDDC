@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 dark:border-white/10 dark:bg-slate-900 dark:text-slate-100 focus:border-sky-500 dark:focus:border-sky-500 focus:ring-sky-500 dark:focus:ring-sky-500 rounded-lg shadow-sm']) }}>
