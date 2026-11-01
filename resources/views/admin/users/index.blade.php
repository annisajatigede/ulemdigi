<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
                {{ __('Manajemen Pengguna (Users)') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-10" x-data="{ addUserModal: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                    <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama atau email..."
                               class="text-xs px-3.5 py-2 rounded-xl border border-stone-200 w-64">
                        <select name="role" class="text-xs px-3 py-2 rounded-xl border border-stone-200">
                            <option value="">Semua Role</option>
                            <option value="user" {{ $role === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        <button type="submit" class="bg-stone-900 text-white text-xs font-bold px-4 py-2 rounded-xl">Filter</button>
                    </form>

                    <button @click="addUserModal = true"
                            class="bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md self-start">
                        + Tambah User
                    </button>
                </div>

                <!-- MODAL TAMBAH USER -->
                <div x-show="addUserModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 p-4">
                    <div @click.away="addUserModal = false" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl">
                        <h4 class="text-base font-bold text-stone-900 mb-4">Tambah Pengguna Baru</h4>
                        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Lengkap *</label>
                                <input type="text" name="name" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Email *</label>
                                <input type="email" name="email" required class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Password *</label>
                                <input type="password" name="password" required minlength="8" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Role *</label>
                                <select name="role" class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                    <option value="user">User Biasa</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="addUserModal = false" class="px-4 py-2 text-xs font-bold text-stone-500">Batal</button>
                                <button type="submit" class="bg-rose-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- TABEL USERS -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-stone-50 text-stone-500 uppercase tracking-wider border-b border-stone-200">
                            <tr>
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">Email</th>
                                <th class="py-3 px-4">Role</th>
                                <th class="py-3 px-4">Undangan</th>
                                <th class="py-3 px-4">Terdaftar</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100">
                            @foreach($users as $u)
                                <tr>
                                    <td class="py-3 px-4 font-bold text-stone-900">{{ $u->name }}</td>
                                    <td class="py-3 px-4 text-stone-600">{{ $u->email }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2 py-0.5 rounded-full font-bold text-[10px] {{ $u->isAdmin() ? 'bg-rose-100 text-rose-800' : 'bg-stone-100 text-stone-700' }}">
                                            {{ strtoupper($u->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-semibold text-stone-700">{{ $u->invitations_count }} Undangan</td>
                                    <td class="py-3 px-4 text-stone-500">{{ $u->created_at->format('d/m/Y') }}</td>
                                    <td class="py-3 px-4 text-right">
                                        @if(auth()->id() !== $u->id)
                                            <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus user ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold">Hapus</button>
                                            </form>
                                        @else
                                            <span class="text-stone-400 italic">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
