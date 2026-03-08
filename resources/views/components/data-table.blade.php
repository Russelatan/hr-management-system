@props([])

<div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700']) }}>
            @if(isset($head))
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        {{ $head }}
                    </tr>
                </thead>
            @endif
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
