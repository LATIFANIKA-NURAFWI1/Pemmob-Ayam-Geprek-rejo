<div class="flex h-full w-full flex-1 flex-col p-6 bg-surface text-on-surface font-body-md antialiased">

    {{-- ── Back Breadcrumb (mobile) ───────────────────────────── --}}
    <div class="md:hidden flex items-center mb-6">
        <a href="{{ route('menu.index') }}" wire:navigate class="mr-4 text-on-surface p-1 rounded-full hover:bg-surface-container-high transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Edit Menu</h1>
    </div>

    {{-- ── Flash Status ─────────────────────────────────────────── --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="mb-4 bg-secondary-container/20 border border-secondary-container text-on-secondary-container px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('status') }}
        </div>
    @endif

    <div class="max-w-6xl w-full mx-auto flex-1 flex flex-col">
        <form wire:submit.prevent="save">
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_4px_24px_rgba(0,0,0,0.04)] border border-[#E9ECEF] flex-1 flex flex-col overflow-hidden">

                {{-- ── Main Form Grid ─────────────────────────── --}}
                <div class="p-6 md:p-8 flex-1 grid grid-cols-1 lg:grid-cols-12 gap-8">

                    {{-- Left: Photo Upload ──────────────────────── --}}
                    <div class="lg:col-span-4 flex flex-col gap-4">
                        <label class="font-body-lg text-body-lg text-on-surface">
                            Foto Menu
                        </label>

                        {{-- Drop zone (click triggers hidden file input) --}}
                        <div class="border-2 border-dashed border-outline-variant rounded-xl bg-[#F1F3F5] flex flex-col items-center justify-center h-64 lg:h-full min-h-[300px] cursor-pointer hover:bg-surface-container-highest transition-colors group relative overflow-hidden"
                             x-data="{ isDropping: false }"
                             x-on:dragover.prevent="isDropping = true"
                             x-on:dragleave.prevent="isDropping = false"
                             x-on:drop.prevent="isDropping = false; if ($event.dataTransfer.files.length > 0) { @this.upload('image', $event.dataTransfer.files[0]) }"
                             x-bind:class="{ 'border-primary bg-primary-container/20': isDropping }"
                             @click="$refs.editPhotoInput.click()">

                            @if($image)
                                {{-- Preview foto baru yang belum disimpan --}}
                                <img src="{{ $image->temporaryUrl() }}"
                                     class="absolute inset-0 w-full h-full object-cover z-10">
                                <div class="absolute inset-0 bg-black/30 z-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-white text-3xl">edit</span>
                                </div>
                            @elseif($existingImage)
                                {{-- Foto lama yang sudah tersimpan --}}
                                <img src="{{ Storage::url($existingImage) }}"
                                     class="absolute inset-0 w-full h-full object-cover z-10">
                                <div class="absolute inset-0 bg-black/30 z-20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="flex flex-col items-center gap-2 text-white">
                                        <span class="material-symbols-outlined text-3xl">edit</span>
                                        <span class="text-sm font-medium">Klik untuk ganti</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex flex-col items-center text-center p-6 z-10">
                                    <div class="w-16 h-16 rounded-full bg-surface flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                        <span class="material-symbols-outlined text-outline text-3xl">add_a_photo</span>
                                    </div>
                                    <p class="font-body-md text-body-md text-on-surface font-medium mb-1">Klik untuk upload foto</p>
                                    <p class="font-label-caps text-label-caps text-on-surface-variant">PNG, JPG up to 5MB</p>
                                </div>
                            @endif

                            <input wire:model="image" type="file" accept="image/*"
                                   class="hidden" x-ref="editPhotoInput">
                        </div>

                        {{-- Upload progress --}}
                        <div wire:loading wire:target="image"
                             class="flex items-center gap-2 text-on-surface-variant text-sm">
                            <span class="material-symbols-outlined text-[18px] animate-spin">sync</span>
                            Mengunggah foto...
                        </div>

                        {{-- Tombol hapus foto jika ada existing image dan belum pilih baru --}}
                        @if($existingImage && !$image)
                            <button type="button" wire:click="deleteImage"
                                    wire:confirm="Hapus foto ini?"
                                    class="flex items-center justify-center gap-2 w-full py-2 rounded-lg border border-error/40 bg-error-container/30 text-error text-sm font-medium hover:bg-error-container transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                Hapus Foto
                            </button>
                        @endif

                        @error('image')
                            <p class="text-error text-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Right: Form Fields ──────────────────────── --}}
                    <div class="lg:col-span-8 flex flex-col gap-6">

                        {{-- Nama Menu --}}
                        <div class="flex flex-col gap-2">
                            <label class="font-body-md text-body-md text-on-surface font-medium" for="editNama">
                                Nama Menu <span class="text-error">*</span>
                            </label>
                            <input wire:model="name" id="editNama" type="text"
                                   placeholder="Contoh: Ayam Geprek Sambal Matah"
                                   class="w-full px-4 py-3 rounded-lg bg-[#F1F3F5] border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-body-md text-body-md text-on-surface placeholder:text-outline">
                            @error('name')
                                <p class="text-error text-sm">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori + Harga (2 kolom) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Kategori --}}
                            <div class="flex flex-col gap-2">
                                <label class="font-body-md text-body-md text-on-surface font-medium" for="editKategori">
                                    Kategori <span class="text-error">*</span>
                                </label>
                                <div class="relative">
                                    <select wire:model="category_id" id="editKategori"
                                            class="w-full px-4 py-3 rounded-lg bg-[#F1F3F5] border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-body-md text-body-md text-on-surface appearance-none cursor-pointer pr-10">
                                        <option value="" disabled>Pilih kategori</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-outline pointer-events-none">expand_more</span>
                                </div>
                                @error('category_id')
                                    <p class="text-error text-sm">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Harga --}}
                            <div class="flex flex-col gap-2">
                                <label class="font-body-md text-body-md text-on-surface font-medium" for="editHarga">
                                    Harga <span class="text-error">*</span>
                                </label>
                                <div class="relative flex items-center">
                                    <span class="absolute left-4 font-body-md text-body-md font-bold text-on-surface-variant pointer-events-none">Rp</span>
                                    <input wire:model="price" id="editHarga" type="number" min="0" placeholder="0"
                                           class="w-full pl-12 pr-4 py-3 rounded-lg bg-[#F1F3F5] border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-body-md text-body-md text-on-surface placeholder:text-outline [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                </div>
                                @error('price')
                                    <p class="text-error text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <div class="flex flex-col gap-2 flex-grow">
                            <label class="font-body-md text-body-md text-on-surface font-medium" for="editDeskripsi">
                                Deskripsi Menu
                            </label>
                            <textarea wire:model="description" id="editDeskripsi" rows="5"
                                      placeholder="Jelaskan detail menu ini (opsional)..."
                                      class="w-full px-4 py-3 rounded-lg bg-[#F1F3F5] border-none focus:ring-2 focus:ring-primary focus:bg-surface-container-lowest transition-all font-body-md text-body-md text-on-surface placeholder:text-outline resize-none"></textarea>
                        </div>

                        {{-- Availability Toggle --}}
                        <div class="flex items-center justify-between mt-4 p-4 rounded-lg bg-surface-container border border-outline-variant/30">
                            <div class="flex flex-col">
                                <span class="font-body-lg text-body-lg font-medium text-on-surface">Menu Tersedia</span>
                                <span class="font-body-md text-body-md text-on-surface-variant">Tampilkan menu ini di kasir dan aplikasi pelanggan.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-4">
                                <input wire:model="is_available" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-outline-variant peer-focus:outline-none rounded-full peer
                                            peer-checked:after:translate-x-full peer-checked:after:border-white
                                            after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                            after:bg-white after:border-gray-300 after:border after:rounded-full
                                            after:h-5 after:w-5 after:transition-all
                                            peer-checked:bg-primary transition-colors duration-200"></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ── Footer Actions ──────────────────────────── --}}
                <div class="p-6 md:p-8 bg-surface border-t border-[#E9ECEF] flex flex-col-reverse sm:flex-row justify-end items-center gap-4">
                    <a href="{{ route('menu.index') }}" wire:navigate
                       class="w-full sm:w-auto px-6 py-3 rounded-lg font-body-md text-body-md font-bold text-on-surface border border-outline-variant hover:bg-surface-container-highest transition-colors active:scale-95 text-center">
                        Batal
                    </a>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                            class="w-full sm:w-auto px-6 py-3 rounded-lg font-body-md text-body-md font-bold bg-primary text-on-primary hover:bg-surface-tint shadow-md hover:shadow-none transition-all active:scale-95 flex items-center justify-center gap-2">
                        <span wire:loading wire:target="save" class="material-symbols-outlined text-xl animate-spin">sync</span>
                        <span wire:loading.remove wire:target="save" class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">save</span>
                        <span wire:loading wire:target="save">Menyimpan...</span>
                        <span wire:loading.remove wire:target="save">Simpan Perubahan</span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
