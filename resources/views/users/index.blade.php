<x-app-layout>
    <style>
        .section-card { border: 1px solid #e9ecef; border-radius: 1rem; background: #fff; box-shadow: 0 2px 12px rgba(0,0,0,.05); overflow: hidden; }
        .card-topbar { height: 4px; width: 100%; }
        .role-badge { padding: 0.35rem 0.8rem; border-radius: 2rem; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; }
    </style>

    <div class="py-5" style="background: #f4f6fb; min-height: 100vh;">
        <div class="container-xl">
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="fw-semibold text-dark mb-1" style="font-size:1.6rem;letter-spacing:-.02em">Manajemen User</h1>
                    <p class="text-muted mb-0" style="font-size:.9rem">Kelola hak akses dan akun pengguna sistem.</p>
                </div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahUser" style="border-radius: .6rem; font-weight: 500;">
                    + Tambah User
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: .6rem;">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: .6rem;">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- ═════ TABEL USER ═════ --}}
            <div class="section-card">
                <div class="card-topbar bg-primary"></div>
                <div class="table-responsive p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead style="background: #f8f9ff; font-size: .8rem; text-transform: uppercase; color: #64748b;">
                            <tr>
                                <th class="py-3 px-4" width="5%">No</th>
                                <th class="py-3 px-4">Nama Lengkap</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4 text-center">Role</th>
                                <th class="py-3 px-4 text-center" width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $index => $user)
                                <tr>
                                    <td class="px-4 text-muted">{{ $index + 1 }}</td>
                                    <td class="px-4 fw-medium text-dark">{{ $user->name }}</td>
                                    <td class="px-4 text-muted">{{ $user->email }}</td>
                                    <td class="px-4 text-center">
                                        @php
                                            $roleColor = match($user->role) {
                                                'superadmin' => 'bg-danger text-white',
                                                'admin' => 'bg-primary text-white',
                                                default => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="role-badge {{ $roleColor }}">{{ $user->role }}</span>
                                    </td>
                                    <td class="px-4 text-center">
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $user->id }}" style="border-radius: .4rem;">Edit</button>
                                        @if(auth()->id() != $user->id)
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="border-radius: .4rem;">Hapus</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>

                                {{-- ═════ MODAL EDIT USER ═════ --}}
                                <div class="modal fade" id="modalEdit{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content" style="border-radius: 1rem; border: none;">
                                            <form action="{{ route('users.update', $user->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header border-0 pb-0">
                                                    <h5 class="modal-title fw-bold">Edit User</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium" style="font-size: .85rem;">Nama Lengkap</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}" required style="border-radius: .5rem;">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium" style="font-size: .85rem;">Email</label>
                                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required style="border-radius: .5rem;">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-medium" style="font-size: .85rem;">Role (Jabatan)</label>
                                                        <select name="role" class="form-select" required style="border-radius: .5rem;">
                                                            <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                                            <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                                                            <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label fw-medium" style="font-size: .85rem;">Password Baru <span class="text-muted fw-normal">(Kosongkan jika tidak ingin ganti)</span></label>
                                                        <input type="password" name="password" class="form-control" style="border-radius: .5rem;">
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="submit" class="btn btn-primary w-100" style="border-radius: .5rem; font-weight: 500;">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ═════ MODAL TAMBAH USER ═════ --}}
            <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 1rem; border: none;">
                        <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <h5 class="modal-title fw-bold">Tambah User Baru</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="font-size: .85rem;">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control" required style="border-radius: .5rem;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="font-size: .85rem;">Email</label>
                                    <input type="email" name="email" class="form-control" required style="border-radius: .5rem;">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-medium" style="font-size: .85rem;">Role (Jabatan)</label>
                                    <select name="role" class="form-select" required style="border-radius: .5rem;">
                                        <option value="user">User (Hanya Lihat)</option>
                                        <option value="admin">Admin (Input Data)</option>
                                        <option value="superadmin">Superadmin (Full Akses)</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-medium" style="font-size: .85rem;">Password</label>
                                    <input type="password" name="password" class="form-control" required style="border-radius: .5rem;" minlength="8">
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="submit" class="btn btn-primary w-100" style="border-radius: .5rem; font-weight: 500;">Simpan User</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>