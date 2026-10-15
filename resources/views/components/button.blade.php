<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 border border-transparent rounded-xl font-bold text-sm text-white shadow-md shadow-blue-500/20 focus:outline-none focus:ring-4 focus:ring-blue-500/20 disabled:opacity-50 active:scale-95 transition-all']) }}>
    {{ $slot }}
</button>
