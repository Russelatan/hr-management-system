@props(['type' => 'info', 'dismissible' => false])

@php
    $styles = match($type) {
        'success' => 'border-green-200 bg-green-50 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300',
        'error'   => 'border-red-200 bg-red-50 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300',
        'warning' => 'border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        default   => 'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    };
@endphp

<div
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
    {{ $attributes->merge(['class' => "flex items-center gap-3 rounded-lg border px-4 py-3 text-sm $styles"]) }}
>
    <div class="flex-1">{{ $slot }}</div>
    @if($dismissible)
        <button @click="show = false" class="shrink-0 opacity-60 hover:opacity-100">
            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
        </button>
    @endif
</div>
