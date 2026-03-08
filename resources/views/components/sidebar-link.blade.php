@props(['href', 'active' => false])

<a href="{{ $href }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors {{ $active ? 'bg-indigo-600/20 text-white border-l-2 border-indigo-400' : 'text-slate-300 hover:bg-white/10 hover:text-white' }}">
    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        {{ $icon }}
    </svg>
    {{ $slot }}
</a>
