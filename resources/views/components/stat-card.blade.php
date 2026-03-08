@props(['label', 'value', 'icon' => null, 'color' => 'indigo'])

@php
    $colorMap = [
        'indigo' => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400',
        'green'  => 'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400',
        'blue'   => 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400',
        'red'    => 'bg-red-50 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'yellow' => 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/30 dark:text-yellow-400',
        'purple' => 'bg-purple-50 text-purple-600 dark:bg-purple-900/30 dark:text-purple-400',
        'orange' => 'bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-400',
    ];
    $iconBg = $colorMap[$color] ?? $colorMap['indigo'];
@endphp

<div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="flex items-center gap-4">
        @if($icon)
            <div class="flex h-12 w-12 items-center justify-center rounded-lg {{ $iconBg }}">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
        <div>
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $value }}</p>
        </div>
    </div>
</div>
