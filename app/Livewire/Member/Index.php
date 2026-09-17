<?php

namespace App\Livewire\Member;

use App\Models\Member;
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
    public string $filterTier = '';

    #[Url]
    public string $filterStatus = '';

    // ─── Detail Panel ────────────────────────────────────────────────────────

    public ?int $viewingMemberId = null;



    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function updatedSearch(): void       { $this->resetPage(); }
    public function updatedFilterTier(): void   { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    // =========================================================================
    // DETAIL VIEW
    // =========================================================================

    public function viewMember(int $id): void
    {
        $this->viewingMemberId = $id;
    }

    public function closeDetail(): void
    {
        $this->viewingMemberId = null;
    }

    #[\Livewire\Attributes\Computed]
    public function viewingMember(): ?Member
    {
        if (! $this->viewingMemberId) return null;
        return Member::with([
            'orders'    => fn ($q) => $q->latest()->limit(10),
            'pointLogs' => fn ($q) => $q->latest()->limit(10),
        ])->find($this->viewingMemberId);
    }

    // =========================================================================
    // TOGGLE ACTIVE
    // =========================================================================

    public function toggleActive(int $id): void
    {
        $m = Member::findOrFail($id);
        $m->update(['is_active' => ! $m->is_active]);
        session()->flash('status', 'Status member diperbarui.');
    }



    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        $members = Member::when($this->search, fn ($q) => $q
                ->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
            )
            ->when($this->filterTier, fn ($q) => $q->where('tier', $this->filterTier))
            ->when($this->filterStatus === 'active', fn ($q) => $q->where('is_active', true))
            ->when($this->filterStatus === 'inactive', fn ($q) => $q->where('is_active', false))
            ->orderByDesc('points')
            ->paginate(20);

        return view('livewire.member.index', compact('members'));
    }
}
