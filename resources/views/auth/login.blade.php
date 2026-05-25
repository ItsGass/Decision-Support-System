<x-guest-layout>

    <div class="min-h-screen flex items-center justify-center bg-[#f8f9fc]">

        <div class="w-full max-w-md">

            <!-- CARD -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-black p-8">

                <!-- alert sukses -->
                @if (session('success'))
                    <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-600 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- TITLE -->
                <div class="mb-4">
    <a href="{{ route('welcome') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-black transition">
       
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
             fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd"
                  d="M15 8a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 0 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 7.5H14.5A.5.5 0 0 1 15 8"/>
        </svg>

        Kembali
    </a>
</div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-1">
                    Login
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Masuk ke sistem prediksi penjualan
                </p>

                <!-- SESSION STATUS -->
                <x-auth-session-status class="mb-4 text-green-600" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- EMAIL -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />

                        <x-text-input id="email"
                            class="block mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            type="email" name="email" :value="old('email')" required autofocus />

                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                    </div>

                    <!-- PASSWORD -->
                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password')" />

                        <x-text-input id="password"
                            class="block mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            type="password" name="password" required />

                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <!-- REMEMBER -->
                    <div class="flex items-center justify-between mt-4">

                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember"
                                class="rounded border-gray-300 text-black focus:ring-black">
                            Remember me
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-gray-500 hover:text-black">
                                Forgot password?
                            </a>
                        @endif

                    </div>

                    <!-- BUTTON -->
                    <div class="mt-6">
                        <button type="submit"
                            class="w-full bg-black text-white py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                            Login
                        </button>
                    </div>

                </form>

                

            </div>

        </div>

    </div>

</x-guest-layout>
