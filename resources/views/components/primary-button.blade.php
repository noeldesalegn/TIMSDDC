<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-sky-500 hover:bg-sky-400 border border-transparent rounded-lg font-semibold text-xs text-slate-950 uppercase tracking-widest shadow-lg shadow-sky-500/40 focus:bg-sky-400 active:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
