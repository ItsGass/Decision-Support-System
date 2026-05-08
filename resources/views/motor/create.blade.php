<x-app-layout>
    <div class="p-8 bg-[#f8f9fc] min-h-screen flex justify-center items-center">

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 w-full max-w-md">

            <!-- 🔙 tombol kembali -->
            <div class="mb-6">
                <a href="{{ route('motor.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                   bg-red-100 text-red-500 hover:bg-red-200 transition">
                    ← Kembali
                </a>
            </div>

            <!-- TITLE -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                Tambah Motor
            </h2>
            <p class="text-sm text-gray-400 mb-6">
                Masukkan nama motor baru
            </p>

            <!-- FORM -->
            <form action="{{ route('motor.store') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-600">
                        Nama Motor
                    </label>

                    <input type="text" name="nama" value="{{ old('nama') }}"
                        class="w-full border rounded-xl px-4 py-2.5 outline-none transition
            {{ $errors->has('nama') ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-black' }}"
                        placeholder="Contoh: Aerox">

                    @error('nama')
                        <p class="text-red-500 text-xs mt-1">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- INPUT KATEGORI -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-600">
                        Kategori
                    </label>

                    <input type="text" name="category" value="{{ old('category') }}"
                        class="w-full border rounded-xl px-4 py-2.5 outline-none transition
        {{ $errors->has('category') ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-black' }}"
                        placeholder="Contoh: Fast Moving / Premium / Slow Moving">

                    @error('category')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 🔥 tombol hitam -->
                <button
                    class="w-full bg-black text-white py-2.5 rounded-xl font-medium
                    hover:bg-gray-800 hover:scale-[1.02] active:scale-95 transition duration-200">
                    Simpan
                </button>

            </form>

        </div>

    </div>
</x-app-layout>
