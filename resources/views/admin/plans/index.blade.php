<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
            {{ __('Manajemen Paket Layanan & Harga') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                @foreach($plans as $plan)
                    <div class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-xs font-mono font-bold text-stone-400 uppercase tracking-wider">{{ $plan->code }}</span>
                                <span class="text-xs font-semibold text-stone-500">Terjual: <strong>{{ $plan->transactions_count }}x</strong></span>
                            </div>

                            <form action="{{ route('admin.plans.update', $plan) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Nama Paket *</label>
                                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" required
                                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Harga (Rupiah) *</label>
                                    <input type="number" name="price" value="{{ old('price', round($plan->price)) }}" required
                                           class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200">
                                </div>

                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-[11px] font-bold text-stone-700 mb-1">Durasi (Hari)</label>
                                        <input type="number" name="duration_days" value="{{ old('duration_days', $plan->duration_days) }}" required
                                               class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Foto</label>
                                        <input type="number" name="max_photos" value="{{ old('max_photos', $plan->max_photos) }}" required
                                               class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-stone-700 mb-1">Maks Acara</label>
                                        <input type="number" name="max_events" value="{{ old('max_events', $plan->max_events) }}" required
                                               class="w-full text-xs px-2 py-1.5 rounded-lg border border-stone-200">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-stone-700 mb-1">Daftar Fitur (Satu per baris)</label>
                                    <textarea name="features_text" rows="6" required
                                              class="w-full text-xs px-3 py-2 rounded-xl border border-stone-200 font-sans">{{ implode("\n", $plan->features ?? []) }}</textarea>
                                </div>

                                <button type="submit"
                                        class="w-full bg-stone-900 hover:bg-stone-800 text-white font-bold text-xs py-2.5 rounded-xl shadow-md transition-colors">
                                    Simpan Paket {{ $plan->name }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
