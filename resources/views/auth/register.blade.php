<x-guest-layout>

    <div class="min-h-screen flex items-center justify-center bg-[#f8f9fc]">

        <div class="w-full max-w-md">

            <!-- CARD -->
            <div class="bg-white rounded-xl shadow-sm border-l-4 border-black p-8">

                <div id="alertBox" class="hidden mb-4 p-3 rounded-lg text-sm font-medium"></div>

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
                    Register
                </h2>

                <p class="text-sm text-gray-500 mb-6">
                    Buat akun untuk mulai menggunakan sistem
                </p>

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- NAME -->
                    <div>
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name"
                            class="block mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                    </div>

                    <!-- EMAIL -->
                    <div>
                        <x-input-label for="email" :value="__('Email')" />

                        <div class="flex gap-2">
                            <x-text-input id="email" class="w-full rounded-lg border-gray-300 focus:ring-purple-500"
                                type="email" name="email" required />

                            <!-- KIRIM KODE -->
                            <button type="button" id="sendOtp"
                                class="px-3 py-2 bg-black text-white rounded-lg text-sm">
                                Kirim
                            </button>
                        </div>

                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                    </div>

                    <!-- OTP -->
                    <div class="mt-4">
                        <x-input-label for="otp" value="Kode Verifikasi" />

                        <input type="text" name="otp" id="otp"
                            class="w-full mt-1 rounded-lg border-gray-300 text-sm"
                            placeholder="Masukkan kode dari email">
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <x-input-label for="password" :value="__('Password')" />
                        <x-text-input id="password"
                            class="block mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            type="password" name="password" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                    </div>

                    <!-- CONFIRM -->
                    <div>
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-text-input id="password_confirmation"
                            class="block mt-1 w-full rounded-lg border-gray-300 focus:border-black focus:ring-black"
                            type="password" name="password_confirmation" required />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                    </div>

                    <!-- BUTTON -->
                    <button
                        class="w-full bg-black text-white py-2.5 rounded-lg font-medium hover:opacity-90 transition">
                        Register
                    </button>

                </form>

                <!-- LOGIN LINK -->
                <p class="text-sm text-gray-500 mt-6 text-center">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-black font-medium hover:underline">
                        Login
                    </a>
                </p>

            </div>

        </div>

    </div>

</x-guest-layout>
<script>
    document.addEventListener('DOMContentLoaded', function () {

    const btn = document.getElementById('sendOtp');

    btn.addEventListener('click', async function () {

        let email = document.getElementById('email').value;

        if (!email) {
            showAlert("Isi email dulu", "error");
            return;
        }

        btn.innerText = "Mengirim...";
        btn.disabled = true;

        fetch('/send-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ email: email })
        })

        // 🔥 INI BAGIAN YANG LO TANYA
        .then(async res => {
            let data = await res.json();

            if (!res.ok) {
                showAlert(data.error || "Gagal kirim OTP", "error");
                return;
            }

            showAlert("Kode OTP berhasil dikirim 📩", "success");
        })

        .catch(() => {
            showAlert("Email salah atau Server error", "error");
        })

        .finally(() => {
            btn.innerText = "Kirim";
            btn.disabled = false;
        });

    });

});

    //alert
    function showAlert(message, type = 'success') {

    const box = document.getElementById('alertBox');

    box.classList.remove('hidden');

    if (type === 'success') {
        box.className = "mb-4 p-3 rounded-lg text-sm font-medium bg-green-100 text-green-600";
    } else {
        box.className = "mb-4 p-3 rounded-lg text-sm font-medium bg-red-100 text-red-500";
    }

    box.innerText = message;

    // auto hide
    setTimeout(() => {
        box.classList.add('hidden');
    }, 3000);
}

</script>
