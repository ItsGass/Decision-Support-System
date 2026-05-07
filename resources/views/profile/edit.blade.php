<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            Profile
        </h2>
    </x-slot>

    <div class="p-6 bg-gray-100 min-h-screen">

        <div class="max-w-5xl mx-auto space-y-8">

            <!-- Profile Info -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Informasi Akun
                </h3>

                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Password -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Ubah Password
                </h3>

                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete -->
            <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">
                    Hapus Akun
                </h3>

                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

        </div>

    </div>
</x-app-layout>
