<?php

namespace App\Livewire\Menu;

use App\Models\Category;
use App\Models\MenuItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Menu Makanan')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $kategori = '';

    #[Url]
    public string $status = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedKategori(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function toggleAvailable(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->update(['is_available' => ! $item->is_available]);
        session()->flash('status', 'Status menu ' . $item->name . ' diperbarui.');
    }

    public function delete(int $id): void
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();
        session()->flash('status', 'Menu ' . $item->name . ' berhasil dihapus.');
    }

    public function render()
    {
        $query = MenuItem::with('category')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->kategori, fn ($q) => $q->where('category_id', $this->kategori))
            ->when($this->status !== '', fn ($q) => $q->where('is_available', (bool) $this->status))
            ->ordered();

        return view('livewire.menu.index', [
            'menuItems'  => $query->paginate(10),
            'categories' => Category::active()->ordered()->get(),
        ]);
    }
}
