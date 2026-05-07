@props(['align' => 'right', 'width' => '56', 'contentClasses' => 'py-2 bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '56' => 'w-56',
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">

    <!-- Trigger -->
    <div @click="open = ! open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    <!-- Dropdown -->
    <div x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-1 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        class="absolute z-50 mt-3 {{ $width }} rounded-xl {{ $alignmentClasses }}"
        style="display: none;"
        @click="open = false">

        <div class="rounded-xl bg-white shadow-lg ring-1 ring-gray-200 overflow-hidden">

            <!-- Header user -->
            <div class="px-4 py-3 border-b bg-gray-50">
                <p class="text-sm font-semibold text-gray-700">
                    {{ Auth::user()->name }}
                </p>
                <p class="text-xs text-gray-400">
                    {{ Auth::user()->email }}
                </p>
            </div>

            <!-- Content -->
            <div class="py-1">
                {{ $content }}
            </div>

        </div>
    </div>
</div>