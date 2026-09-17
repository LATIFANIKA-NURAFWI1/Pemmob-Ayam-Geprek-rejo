<?php

namespace App\Livewire\Stok;

use App\Models\StockIngredient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title("Manajemen Stok Bahan")]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = "";

    #[Url]
    public string $filterStatus = ""; // '' | 'low' | 'ok'

    public bool $showForm = false;
    public bool $showAdjustModal = false;

    public ?int $editingId = null;
    public ?int $adjustingId = null;

    // ── Form fields ──────────────────────────────────────────────────────────
    public string $name = "";
    public string $unit = "";
    public float $current_stock = 0;
    public float $minimum_stock = 10;
    public float $unit_cost = 0;

    // ── Adjustment fields ────────────────────────────────────────────────────
    public float $adjustQty = 0;
    public string $adjustNote = "";

    // ── Lifecycle ────────────────────────────────────────────────────────────

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    // ── CRUD ─────────────────────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->reset([
            "editingId",
            "name",
            "unit",
            "current_stock",
            "minimum_stock",
            "unit_cost",
        ]);
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $ingredient = StockIngredient::findOrFail($id);

        $this->editingId = $id;
        $this->name = $ingredient->name;
        $this->unit = $ingredient->unit;
        $this->current_stock = (float) $ingredient->current_stock;
        $this->minimum_stock = (float) $ingredient->minimum_stock;
        $this->unit_cost = (float) $ingredient->unit_cost;
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate([
            "name"          => "required|min:2|max:100",
            "unit"          => "required|max:20",
            "current_stock" => "required|numeric|min:0",
            "minimum_stock" => "required|numeric|min:0",
            "unit_cost"     => "required|numeric|min:0",
        ]);

        $data = [
            "name"          => $this->name,
            "unit"          => $this->unit,
            "current_stock" => $this->current_stock,
            "minimum_stock" => $this->minimum_stock,
            "unit_cost"     => $this->unit_cost,
        ];

        $isEdit = (bool) $this->editingId;

        if ($isEdit) {
            StockIngredient::findOrFail($this->editingId)->update($data);
            session()->flash("status", "Bahan baku \"{$this->name}\" berhasil diperbarui.");
        } else {
            StockIngredient::create($data);
            session()->flash("status", "Bahan baku \"{$this->name}\" berhasil ditambahkan.");
        }

        $this->showForm = false;
        $this->filterStatus = ''; // Reset filter agar item yang baru diperbarui selalu terlihat
        $this->reset([
            "editingId",
            "name",
            "unit",
            "current_stock",
            "minimum_stock",
            "unit_cost",
        ]);
        $this->resetValidation();
        $this->resetPage();
    }

    public function delete(int $id): void
    {
        StockIngredient::findOrFail($id)->delete();
        session()->flash("status", "Bahan baku dihapus.");
    }

    public function openAdjust(int $id): void
    {
        $this->adjustingId = $id;
        $this->adjustQty = 0;
        $this->adjustNote = "";
        $this->showAdjustModal = true;
    }

    public function applyAdjustment(): void
    {
        $this->validate(["adjustQty" => "required|numeric"]);

        $ingredient = StockIngredient::findOrFail($this->adjustingId);
        $newQty = max(
            0,
            (float) $ingredient->current_stock + $this->adjustQty,
        );
        $ingredient->update(["current_stock" => $newQty]);

        session()->flash("status", "Stok {$ingredient->name} disesuaikan.");

        $this->showAdjustModal = false;
        $this->reset(["adjustingId", "adjustQty", "adjustNote"]);
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $ingredients = StockIngredient::query()
            ->when(
                $this->search,
                fn($q) => $q->where("name", "like", "%{$this->search}%"),
            )
            ->when($this->filterStatus === 'low', fn($q) => $q->whereColumn('current_stock', '<', 'minimum_stock'))
            ->when($this->filterStatus === 'ok',  fn($q) => $q->whereColumn('current_stock', '>=', 'minimum_stock'))
            ->orderBy("name")
            ->paginate(20);

        return view("livewire.stok.index", compact("ingredients"));
    }
}
