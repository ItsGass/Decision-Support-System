<x-app-layout>
    <div class="p-8 bg-[#f8f9fc] min-h-screen flex justify-center items-center">

        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100 w-full max-w-md">

            <!-- 🔙 Tombol Kembali -->
            <div class="mb-6">
                <a href="{{ route('motor.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium
                   bg-red-100 text-red-500 hover:bg-red-200 transition">
                    ← Kembali
                </a>
            </div>

            <!-- TITLE -->
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">
                Edit Motor
            </h2>
            <p class="text-sm text-gray-400 mb-6">
                Ubah nama motor
            </p>

            <!-- FORM -->
            <form action="{{ route('motor.update', $motor->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- INPUT NAMA -->
                <div class="mb-5">
                    <label class="block mb-2 text-sm font-medium text-gray-600">
                        Nama Motor
                    </label>

                    <input type="text" name="nama" value="{{ old('nama', $motor->nama) }}"
                        class="w-full border rounded-xl px-4 py-2.5 outline-none transition
                        {{ $errors->has('nama') ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-black focus:scale-[1.01]' }}"
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

    <input type="text" name="category" value="{{ old('category', $motor->category) }}"
        class="w-full border rounded-xl px-4 py-2.5 outline-none transition
        {{ $errors->has('category') ? 'border-red-400 ring-2 ring-red-200' : 'border-gray-200 focus:ring-2 focus:ring-black focus:scale-[1.01]' }}"
        placeholder="Contoh: Matic / Sport / Bebek">

    @error('category')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

                <!-- 🔥 BUTTON UPDATE -->
                <button
                    class="w-full bg-black text-white py-2.5 rounded-xl font-medium
                    hover:bg-gray-800 hover:scale-[1.02] active:scale-95 transition duration-200">
                    Update
                </button>

            </form>

        </div>

    </div>
</x-app-layout>
