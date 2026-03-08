@props(['message' => 'No data found.', 'icon' => null])

<div class="py-12 text-center">
    @if($icon)
        <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700">
            <svg class="h-6 w-6 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                {!! $icon !!}
            </svg>
        </div>
    @endif
    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $message }}</p>
</div>
