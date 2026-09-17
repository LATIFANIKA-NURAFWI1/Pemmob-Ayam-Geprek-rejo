<?php

namespace App\Livewire\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithFileUploads;

    public int $menuId;

    #[Validate('required|string|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:500')]
    public string $description = '';

    #[Validate('required|numeric|min:0')]
    public string $price = '';

    #[Validate('required|integer|exists:categories,id')]
    public string $category_id = '';

    #[Validate('boolean')]
    public bool $is_available = true;

    #[Validate('nullable|image|mimes:jpg,jpeg,png,webp|max:2048')]
    public $image = null;

    public ?string $existingImage = null;

    public function mount(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $this->menuId        = $item->id;
        $this->name          = $item->name;
        $this->description   = $item->description ?? '';
        $this->price         = (string) $item->price;
        $this->category_id   = (string) $item->category_id;
        $this->is_available  = (bool) $item->is_available;
        $this->existingImage = $item->image;
    }

    // Reset image supaya bisa upload ulang setelah preview
    public function updatedImage(): void
    {
        $this->validateOnly('image');
    }

    public function save(): void
    {
        $this->validate();

        $item = MenuItem::findOrFail($this->menuId);

        $data = [
            'name'         => $this->name,
            'description'  => $this->description,
            'price'        => $this->price,
            'category_id'  => $this->category_id,
            'is_available' => $this->is_available,
        ];

        if ($this->image) {
            // Hapus gambar lama kalau ada
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $data['image'] = $this->image->store('menu', 'public');
            $this->image   = null;
        }

        $item->update($data);

        session()->flash('status', 'Menu berhasil diperbarui.');
        $this->redirect(route('menu.index'), navigate: true);
    }

    public function deleteImage(): void
    {
        if ($this->existingImage) {
            Storage::disk('public')->delete($this->existingImage);
            MenuItem::findOrFail($this->menuId)->update(['image' => null]);
            $this->existingImage = null;
            $this->image         = null;
        }
        session()->flash('status', 'Foto menu dihapus.');
    }

    public function render()
    {
        return view('livewire.menu.edit', [
            'categories' => Category::active()->orderBy('name')->get(),
        ]);
    }
}
