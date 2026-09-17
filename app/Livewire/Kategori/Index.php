<?php

namespace App\Livewire\Kategori;

use App\Models\Category;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $sortField = 'name';

    #[Url]
    public string $sortDirection = 'asc';

    public bool $showModal = false;
    public ?int $editId = null;
    public string $name = '';
    public bool $is_active = true;

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreate(): void
    {
        $this->reset(['editId', 'name']);
        $this->is_active = true;
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editId     = $cat->id;
        $this->name       = $cat->name;
        $this->is_active  = $cat->is_active;
        $this->showModal  = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:100',
        ]);

        $data = [
            'name'       => $this->name,
            'slug'       => \Str::slug($this->name),
            'is_active'  => $this->is_active,
        ];

        if ($this->editId) {
            Category::findOrFail($this->editId)->update($data);
        } else {
            Category::create($data);
        }

        $this->showModal = false;
        session()->flash('status', $this->editId ? 'Kategori berhasil diperbarui.' : 'Kategori berhasil ditambahkan.');
        $this->reset(['editId', 'name']);
        $this->is_active = true;
    }

    public function delete(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->delete();
        session()->flash('status', 'Kategori ' . $cat->name . ' berhasil dihapus.');
    }

    public function render()
    {
        $query = Category::withCount('menuItems')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"));

        if ($this->filterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->filterStatus === 'inactive') {
            $query->where('is_active', false);
        }

        if ($this->sortField === 'menu_items_count') {
            $query->orderBy('menu_items_count', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $categories = $query->paginate(15);

        return view('livewire.kategori.index', compact('categories'));
    }
}
