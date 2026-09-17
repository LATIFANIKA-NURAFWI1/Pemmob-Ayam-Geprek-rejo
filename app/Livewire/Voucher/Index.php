<?php

namespace App\Livewire\Voucher;

use App\Models\Voucher;
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
    public string $filterStatus = '';

    // ─── Form ────────────────────────────────────────────────────────────────

    public bool   $showForm  = false;
    public ?int   $editingId = null;

    public string  $formCode          = '';
    public string  $formName          = '';   // nama voucher (deskripsi singkat)
    public string  $formDiscountType  = 'percentage';
    public string  $formDiscountValue = '';
    public string  $formMinPurchase   = '0';
    public string  $formStartDate     = '';
    public string  $formEndDate       = '';
    public string  $formMaxUses       = '';
    public bool    $formIsActive      = true;
    public bool    $formMemberOnly    = false;

    // ─── Delete ──────────────────────────────────────────────────────────────

    public ?int $deletingId = null;

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $this->formStartDate = today()->toDateString();
        $this->formEndDate   = today()->addMonth()->toDateString();
    }

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    // =========================================================================
    // FORM ACTIONS
    // =========================================================================

    public function openCreate(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->showForm  = true;
    }

    public function openEdit(int $id): void
    {
        $v = Voucher::findOrFail($id);

        $this->editingId         = $id;
        $this->formCode          = $v->code;
        $this->formName          = $v->name ?? '';
        $this->formDiscountType  = $v->discount_type;
        $this->formDiscountValue = (string) $v->discount_value;
        $this->formMinPurchase   = (string) (int) ($v->minimum_order ?? 0);
        $this->formStartDate     = $v->starts_at ? $v->starts_at->toDateString() : today()->toDateString();
        $this->formEndDate       = $v->expires_at ? $v->expires_at->toDateString() : today()->addMonth()->toDateString();
        $this->formMaxUses       = $v->max_uses ? (string) $v->max_uses : '';
        $this->formIsActive      = (bool) $v->is_active;
        $this->formMemberOnly    = (bool) $v->member_only;
        $this->showForm          = true;
    }

    public function saveVoucher(): void
    {
        $rules = [
            'formCode'          => ['required', 'min:3', 'max:50', 'alpha_dash'],
            'formName'          => ['nullable', 'string', 'max:100'],
            'formDiscountType'  => ['required', 'in:percentage,fixed'],
            'formDiscountValue' => ['required', 'numeric', 'min:0.01'],
            'formMinPurchase'   => ['required', 'numeric', 'min:0'],
            'formStartDate'     => ['nullable', 'date'],
            'formEndDate'       => ['nullable', 'date'],
            'formMaxUses'       => ['nullable', 'integer', 'min:1'],
            'formMemberOnly'    => ['required', 'boolean'],
        ];

        // Validasi diskon persentase max 100
        if ($this->formDiscountType === 'percentage') {
            $rules['formDiscountValue'][] = 'max:100';
        }

        // after_or_equal jika keduanya diisi
        if ($this->formStartDate && $this->formEndDate) {
            $rules['formEndDate'][] = 'after_or_equal:formStartDate';
        }

        // Kode harus unik kecuali saat edit
        if ($this->editingId) {
            $rules['formCode'][] = \Illuminate\Validation\Rule::unique('vouchers', 'code')->ignore($this->editingId);
        } else {
            $rules['formCode'][] = \Illuminate\Validation\Rule::unique('vouchers', 'code');
        }

        $this->validate($rules, [
            'formCode.required'          => 'Kode voucher wajib diisi.',
            'formCode.unique'            => 'Kode voucher sudah digunakan.',
            'formCode.alpha_dash'        => 'Kode hanya boleh huruf, angka, dan tanda hubung.',
            'formDiscountValue.required' => 'Nilai diskon wajib diisi.',
            'formEndDate.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal mulai.',
        ]);

        // Mapping ke nama kolom model yang sebenarnya
        $payload = [
            'code'          => strtoupper($this->formCode),
            'name'          => $this->formName ?: strtoupper($this->formCode), // fallback ke kode jika kosong
            'discount_type' => $this->formDiscountType,
            'discount_value' => (float) $this->formDiscountValue,
            'minimum_order' => (float) $this->formMinPurchase,
            'starts_at'     => $this->formStartDate ?: null,
            'expires_at'    => $this->formEndDate ?: null,
            'max_uses'      => $this->formMaxUses ? (int) $this->formMaxUses : null,
            'is_active'     => $this->formIsActive,
            'member_only'   => filter_var($this->formMemberOnly, FILTER_VALIDATE_BOOLEAN),
        ];

        if ($this->editingId) {
            Voucher::findOrFail($this->editingId)->update($payload);
            session()->flash('status', 'Voucher berhasil diperbarui.');
        } else {
            Voucher::create($payload);
            session()->flash('status', 'Voucher berhasil dibuat.');
        }

        $this->closeForm();
    }

    public function closeForm(): void
    {
        $this->showForm  = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function toggleActive(int $id): void
    {
        $v = Voucher::findOrFail($id);
        $v->update(['is_active' => ! $v->is_active]);
        session()->flash('status', 'Status voucher diperbarui.');
    }

    // =========================================================================
    // DELETE
    // =========================================================================

    public function confirmDelete(int $id): void { $this->deletingId = $id; }
    public function cancelDelete(): void          { $this->deletingId = null; }

    public function delete(): void
    {
        if ($this->deletingId) {
            Voucher::findOrFail($this->deletingId)->delete();
            session()->flash('status', 'Voucher berhasil dihapus.');
        }
        $this->deletingId = null;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        $vouchers = Voucher::when($this->search, fn ($q) => $q
                ->where('code', 'like', "%{$this->search}%")
                ->orWhere('name', 'like', "%{$this->search}%")
            )
            ->when($this->filterStatus === 'active',   fn ($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20);

        return view('livewire.voucher.index', compact('vouchers'));
    }

    // =========================================================================
    // PRIVATE
    // =========================================================================

    private function resetForm(): void
    {
        $this->formCode          = '';
        $this->formName          = '';
        $this->formDiscountType  = 'percentage';
        $this->formDiscountValue = '';
        $this->formMinPurchase   = '0';
        $this->formStartDate     = today()->toDateString();
        $this->formEndDate       = today()->addMonth()->toDateString();
        $this->formMaxUses       = '';
        $this->formIsActive      = true;
        $this->formMemberOnly    = false;
    }
}
