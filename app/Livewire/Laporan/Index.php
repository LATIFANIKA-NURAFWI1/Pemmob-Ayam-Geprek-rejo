<?php

namespace App\Livewire\Laporan;

use App\Services\ProfitLossService;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Index extends Component
{
    #[Url]
    public string $dari = '';

    #[Url]
    public string $sampai = '';

    #[Url]
    public string $preset = 'bulanan';

    /** Bulan yang dipilih saat preset = 'bulanan' (format: 1-12) */
    #[Url]
    public int $selectedMonth;

    /** Tahun yang dipilih saat preset = 'tahun' */
    #[Url]
    public int $selectedYear;

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(): void
    {
        $this->selectedMonth = (int) now()->format('m');
        $this->selectedYear  = (int) now()->format('Y');

        $this->applyPreset($this->preset);
    }

    // =========================================================================
    // PRESET SHORTCUTS
    // =========================================================================

    public function applyPreset(string $p): void
    {
        $this->preset = $p;
        $this->resolveRange();
    }

    public function updatedSelectedMonth(): void 
    { 
        $this->preset = 'bulanan';
        $this->resolveRange(); 
    }
    
    public function updatedSelectedYear(): void  
    { 
        if ($this->preset === 'hari_ini' || $this->preset === 'minggu_ini') {
            $this->preset = 'bulanan';
        }
        $this->resolveRange(); 
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    #[Computed]
    public function report(): array
    {
        if (! $this->dari || ! $this->sampai) {
            return [];
        }
        return app(ProfitLossService::class)->calculate($this->dari, $this->sampai);
    }

    #[Computed]
    public function dailyTrend(): array
    {
        if (! $this->dari || ! $this->sampai) {
            return [];
        }
        return app(ProfitLossService::class)->dailyTrend($this->dari, $this->sampai);
    }

    #[Computed]
    public function topItems(): array
    {
        if (! $this->dari || ! $this->sampai) {
            return [];
        }
        return app(ProfitLossService::class)->topMenuItems($this->dari, $this->sampai, 10);
    }

    // =========================================================================
    // EXPORT
    // =========================================================================

    public function exportPdf()
    {
        $report = $this->report;
        $topItems = $this->topItems;
        $dari = $this->dari;
        $sampai = $this->sampai;

        if (empty($report)) {
            session()->flash('error', 'Data tidak tersedia untuk diekspor.');
            return;
        }

        $pdf = Pdf::loadView('exports.laporan-labarugi', compact('report', 'topItems', 'dari', 'sampai'));
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Laporan_Laba_Rugi_' . $dari . '_to_' . $sampai . '.pdf');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.laporan.index');
    }

    // =========================================================================
    // PROTECTED
    // =========================================================================

    protected function resolveRange(): void
    {
        match ($this->preset) {
            'hari_ini'   => [$this->dari, $this->sampai] = [today()->toDateString(), today()->toDateString()],
            'minggu_ini' => [$this->dari, $this->sampai] = [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()],
            'bulanan'    => [$this->dari, $this->sampai] = [
                now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->startOfMonth()->toDateString(),
                now()->setYear($this->selectedYear)->setMonth($this->selectedMonth)->endOfMonth()->toDateString(),
            ],
            'tahun'      => [$this->dari, $this->sampai] = [
                now()->setYear($this->selectedYear)->startOfYear()->toDateString(),
                now()->setYear($this->selectedYear)->endOfYear()->toDateString(),
            ],
            default      => null,
        };
    }
}
