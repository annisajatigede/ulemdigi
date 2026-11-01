<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-stone-900 leading-tight">
            {{ __('Pengaturan Situs & Payment Gateway') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold rounded-2xl">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl border border-stone-200 shadow-sm p-6 sm:p-8 space-y-8">
                @csrf

                <!-- 1. Identitas Situs & SEO -->
                <div>
                    <h3 class="text-base font-extrabold text-stone-900 mb-1">Identitas Situs & Informasi Meta</h3>
                    <p class="text-xs text-stone-500 mb-4">Pengaturan nama platform, deskripsi untuk SEO Google, dan kontak.</p>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Nama Situs *</label>
                                <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Tagline Platform *</label>
                                <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}" required
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Deskripsi Meta (SEO & Social Share) *</label>
                            <textarea name="site_description" rows="3" required
                                      class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200">{{ old('site_description', $settings['site_description']) }}</textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">No. WhatsApp Bantuan</label>
                                <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="6281234567890">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Email Dukungan</label>
                                <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}"
                                       class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200" placeholder="support@kawinan.id">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Upload Logo Platform</label>
                                <input type="file" name="logo" accept="image/*" class="w-full text-xs text-stone-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-stone-700 mb-1">Upload Favicon (.ico / .png)</label>
                                <input type="file" name="favicon" accept="image/*" class="w-full text-xs text-stone-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pengaturan Payment Gateway Midtrans -->
                <div class="pt-6 border-t border-stone-200">
                    <h3 class="text-base font-extrabold text-stone-900 mb-1">Konfigurasi Midtrans Snap Sandbox / Production</h3>
                    <p class="text-xs text-stone-500 mb-4">Kredensial API Midtrans untuk checkout Snap dan verifikasi webhook.</p>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Midtrans Server Key</label>
                            <input type="text" name="midtrans_server_key" value="{{ old('midtrans_server_key', $settings['midtrans_server_key']) }}"
                                   class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-stone-200">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Midtrans Client Key</label>
                            <input type="text" name="midtrans_client_key" value="{{ old('midtrans_client_key', $settings['midtrans_client_key']) }}"
                                   class="w-full text-xs font-mono px-3.5 py-2.5 rounded-xl border border-stone-200">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-stone-700 mb-1">Mode Lingkungan Midtrans</label>
                            <select name="midtrans_is_production" class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-stone-200">
                                <option value="0" {{ $settings['midtrans_is_production'] == '0' ? 'selected' : '' }}>Sandbox (Pengujian)</option>
                                <option value="1" {{ $settings['midtrans_is_production'] == '1' ? 'selected' : '' }}>Production (Live Pembayaran Riil)</option>
                            </select>
                        </div>

                        <div class="p-4 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600">
                            <span class="font-bold text-stone-800">URL Webhook Notification Midtrans:</span>
                            <code class="block font-mono bg-white p-2 rounded border border-stone-200 text-rose-600 mt-1 select-all">
                                {{ url('/payment/midtrans/webhook') }}
                            </code>
                            <p class="text-[11px] text-stone-400 mt-1">Masukkan URL ini ke dashboard Midtrans Anda di menu Settings → Configuration → Payment Notification URL.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-stone-200 flex justify-end">
                    <button type="submit"
                            class="bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm px-8 py-3 rounded-2xl shadow-lg shadow-rose-600/25 transition-all">
                        Simpan Pengaturan
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
