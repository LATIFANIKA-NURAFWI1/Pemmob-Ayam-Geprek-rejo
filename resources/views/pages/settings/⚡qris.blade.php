<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

new #[Title('Pengaturan QRIS')]
#[Layout('layouts.app')]
class extends Component {
    use WithFileUploads;

    #[Validate('nullable|image|mimes:jpg,jpeg,png,webp|max:2048')]
    public $qrisImage = null;

    public ?string $existingQris = null;

    public string $successMessage = '';
    public string $errorMessage   = '';

    public function mount(): void
    {
        $this->existingQris = Setting::get('qris_image_path');
    }

    public function updatedQrisImage(): void
    {
        $this->validateOnly('qrisImage');
        $this->successMessage = '';
        $this->errorMessage   = '';
    }

    public function save(): void
    {
        $this->successMessage = '';
        $this->errorMessage   = '';

        $this->validate();

        if (! $this->qrisImage) {
            $this->errorMessage = 'Pilih atau drag gambar QRIS terlebih dahulu.';
            return;
        }

        // Hapus file lama jika ada
        if ($this->existingQris) {
            Storage::disk('public')->delete($this->existingQris);
        }

        $path = $this->qrisImage->store('qris', 'public');
        Setting::set('qris_image_path', $path);

        $this->existingQris  = $path;
        $this->qrisImage     = null;
        $this->successMessage = 'Gambar QRIS berhasil diperbarui!';
    }

    public function deleteQris(): void
    {
        if ($this->existingQris) {
            Storage::disk('public')->delete($this->existingQris);
            Setting::set('qris_image_path', null);
            $this->existingQris  = null;
        }
        $this->qrisImage     = null;
        $this->successMessage = 'Gambar QRIS berhasil dihapus.';
    }

}; ?>

<x-pages::settings.layout
    :heading="__('QRIS Pembayaran')"
    :subheading="__('Upload gambar QRIS statis agar pelanggan bisa langsung scan saat checkout.')">

    <div class="space-y-6">

        {{-- Success message --}}
        @if($successMessage)
            <div class="flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                <svg class="h-5 w-5 flex-shrink-0 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-sm font-medium text-green-800">{{ $successMessage }}</p>
            </div>
        @endif

        {{-- Error message --}}
        @if($errorMessage)
            <div class="flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                <svg class="h-5 w-5 flex-shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-sm font-medium text-red-700">{{ $errorMessage }}</p>
            </div>
        @endif

        {{-- Preview / Drop Zone --}}
        <div
            id="qris-drop-zone"
            class="relative flex h-64 w-full cursor-pointer items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-zinc-300 bg-zinc-50 transition-all duration-200 dark:border-zinc-600 dark:bg-zinc-800"
            onclick="document.getElementById('qris-file-input').click()"
            ondragover="event.preventDefault(); this.classList.add('border-orange-400','bg-orange-50','dark:bg-zinc-700');"
            ondragleave="this.classList.remove('border-orange-400','bg-orange-50','dark:bg-zinc-700');"
            ondragenter="event.preventDefault();"
            ondrop="event.preventDefault();
                    this.classList.remove('border-orange-400','bg-orange-50','dark:bg-zinc-700');
                    const file = event.dataTransfer.files[0];
                    if (file && file.type.startsWith('image/')) {
                        const input = document.getElementById('qris-file-input');
                        const dt = new DataTransfer();
                        dt.items.add(file);
                        input.files = dt.files;
                        input.dispatchEvent(new Event('change', { bubbles: true }));
                    }"
        >
            @if($qrisImage)
                {{-- Preview foto baru --}}
                <img src="{{ $qrisImage->temporaryUrl() }}" class="h-full w-full object-contain p-4">
                <div class="absolute inset-0 flex items-end justify-center pb-3 opacity-0 hover:opacity-100 transition-opacity">
                    <span class="rounded-full bg-black/50 px-3 py-1 text-xs font-semibold text-white">Klik / drag untuk ganti</span>
                </div>
            @elseif($existingQris)
                {{-- QRIS yang sudah tersimpan --}}
                <img src="{{ Storage::url($existingQris) }}?v={{ time() }}" class="h-full w-full object-contain p-4">
                <div class="absolute inset-0 flex items-end justify-center pb-3 opacity-0 hover:opacity-100 transition-opacity">
                    <span class="rounded-full bg-black/50 px-3 py-1 text-xs font-semibold text-white">Klik / drag untuk ganti</span>
                </div>
            @else
                {{-- Empty state --}}
                <div class="flex flex-col items-center gap-3 text-zinc-400 pointer-events-none">
                    <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12v.01M12 3.01V3M4 20h4m-4 0V4m0 0h4m12 0h-4m4 0v4m0 12v-4M4 8h4V4M4 4h4m12 0h-4m4 0v4m-4 16v-4m0 4h-4"/>
                    </svg>
                    <p class="text-sm font-medium">Drag & drop gambar QRIS ke sini</p>
                    <p class="text-xs">atau klik untuk memilih file</p>
                </div>
            @endif
        </div>

        {{-- Hidden file input --}}
        <input type="file" id="qris-file-input" wire:model="qrisImage" accept="image/*" class="sr-only">

        {{-- Upload progress --}}
        <div wire:loading wire:target="qrisImage" class="flex items-center gap-2 text-sm text-orange-500">
            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
            </svg>
            Mengupload...
        </div>

        @error('qrisImage')
            <p class="text-xs text-red-500">{{ $message }}</p>
        @enderror

        <p class="text-xs text-zinc-400">Format: JPG, PNG, WebP. Ukuran maks 2MB. Disarankan gambar persegi (1:1).</p>

        {{-- Action buttons --}}
        <div class="flex flex-wrap items-center gap-3">
            {{-- Pilih file button --}}
            <label for="qris-file-input" class="cursor-pointer">
                <span class="inline-flex items-center gap-2 rounded-lg border border-zinc-300 bg-white px-4 py-2 text-sm font-medium text-zinc-700 transition hover:bg-zinc-50 dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Pilih Foto
                </span>
            </label>

            {{-- Save button --}}
            @if($qrisImage)
                <button type="button" wire:click="save"
                    wire:loading.attr="disabled" wire:loading.class="opacity-60"
                    wire:target="save"
                    class="inline-flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 disabled:opacity-60">
                    <span wire:loading.remove wire:target="save">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                    <span wire:loading wire:target="save">
                        <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                    </span>
                    <span wire:loading.remove wire:target="save">Simpan QRIS</span>
                    <span wire:loading wire:target="save">Menyimpan...</span>
                </button>
            @endif

            {{-- Delete button --}}
            @if($existingQris && !$qrisImage)
                <button type="button" wire:click="deleteQris"
                    wire:confirm="Hapus gambar QRIS ini? Pelanggan tidak bisa scan QRIS sampai Anda mengupload yang baru."
                    class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-medium text-red-600 transition hover:bg-red-100">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus QRIS
                </button>
            @endif
        </div>

        {{-- Info box --}}
        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3 dark:border-blue-900/30 dark:bg-blue-900/20">
            <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-1">ℹ️ Cara kerja QRIS Statis</p>
            <p class="text-xs text-blue-700 dark:text-blue-400 leading-relaxed">
                Gambar yang Anda upload akan ditampilkan langsung kepada pelanggan di halaman pembayaran saat memilih metode QRIS.
                Pelanggan cukup scan QR tersebut dengan aplikasi dompet digital (GoPay, OVO, Dana, dll.) dan membayar sesuai nominal pesanan.
            </p>
        </div>

    </div>
</x-pages::settings.layout>
