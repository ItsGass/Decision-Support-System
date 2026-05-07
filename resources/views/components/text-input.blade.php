@props(['disabled' => false])

<input {{ $attributes->merge([
'class' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm w-full bg-white text-gray-800'
]) }}>