<?php

namespace App\Livewire\Pengeluaran;

use App\Models\Expense;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;

    // ─── Filter ──────────────────────────────────────────────────────────────

    #[Url]
    public string $search = '';

    #[Url]
    public string $bulan = '';

    #[Url]
    public string $filterKategori = '';

    // ─── Form (Create/Edit) ──────────────────────────────────────────────────

    public bool   $showForm     = false;
    public ?int   $editingId    = null;

    public string $formDate        = '';
    public string $formCategory    = 'lainnya';
    public string $formDescription = '';
    public string $formAmount      = '';

    // ─── Delete Confirmation ─────────────────────────────────────────────────

    public ?int $deletingId = null;

    // ─── Constants ───────────────────────────────────────────────────────────

    public const CATEGORIES = ['bahan_baku', 'operasional', 'gaji', 'perawatan', 'lainnya'];

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $this->bulan    = $this->bulan ?: today()->format('Y-m');
        $this->formDate = today()->toDateString();
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    #[Computed]
    public function categoryTotals(): array
    {
        [$year, $month] = $this->parseBulan();

        return Expense::whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->selectRaw('category, COALESCE(SUM(amount), 0) as total')
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v)
            ->toArray();
    }

    #[Computed]
    public function totalBulanIni(): float
    {
        return (float) array_sum($this->categoryTotals);
    }

    // =========================================================================
    // WATCHERS
    // =========================================================================

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedBulan(): void        { $this->resetPage(); }
    public function updatedFilterKategori(): void { $this->resetPage(); }

    // =========================================================================
    // FORM ACTIONS
    // =========================================================================

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm  = true;
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $expense = Expense::findOrFail($id);

        $this->editingId       = $id;
        $this->formDate        = $expense->expense_date->toDateString();
        $this->formCategory    = $expense->category;
        $this->formDescription = $expense->description;
        $this->formAmount      = (string) (int) $expense->amount;
        $this->showForm        = true;
    }

    public function saveExpense(): void
    {
        $data = $this->validate([
            'formDate'        => 'required|date',
            'formCategory'    => ['required', Rule::in(self::CATEGORIES)],
            'formDescription' => 'required|min:3|max:255',
            'formAmount'      => 'required|numeric|min:1|max:9999999999',
        ], [
            'formDate.required'        => 'Tanggal wajib diisi.',
            'formCategory.required'    => 'Kategori wajib dipilih.',
            'formDescription.required' => 'Deskripsi wajib diisi.',
            'formDescription.min'      => 'Deskripsi minimal 3 karakter.',
            'formAmount.required'      => 'Jumlah wajib diisi.',
            'formAmount.min'           => 'Jumlah minimal Rp 1.',
        ]);

        $payload = [
            'expense_date' => $data['formDate'],
            'category'     => $data['formCategory'],
            'description'  => $data['formDescription'],
            'amount'       => (float) $data['formAmount'],
            'user_id'      => auth()->id(),
        ];

        if ($this->editingId) {
            Expense::findOrFail($this->editingId)->update($payload);
            session()->flash('status', 'Pengeluaran berhasil diperbarui.');
        } else {
            Expense::create($payload);
            session()->flash('status', 'Pengeluaran berhasil dicatat.');
        }

        $this->closeForm();
        unset($this->categoryTotals);
    }

    public function closeForm(): void
    {
        $this->showForm  = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    // =========================================================================
    // DELETE
    // =========================================================================

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Expense::findOrFail($this->deletingId)->delete();
            session()->flash('status', 'Pengeluaran berhasil dihapus.');
        }
        $this->deletingId = null;
        unset($this->categoryTotals);
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        [$year, $month] = $this->parseBulan();

        $expenses = Expense::when($this->search, fn ($q) => $q->where('description', 'like', "%{$this->search}%"))
            ->when($this->filterKategori, fn ($q) => $q->where('category', $this->filterKategori))
            ->whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->latest('expense_date')
            ->paginate(20);

        return view('livewire.pengeluaran.index', [
            'expenses'      => $expenses,
            'totalBulanIni' => $this->totalBulanIni,
        ]);
    }

    // =========================================================================
    // PRIVATE
    // =========================================================================

    private function parseBulan(): array
    {
        return explode('-', $this->bulan ?: today()->format('Y-m'));
    }

    private function resetForm(): void
    {
        $this->formDate        = today()->toDateString();
        $this->formCategory    = 'lainnya';
        $this->formDescription = '';
        $this->formAmount      = '';
    }
}
