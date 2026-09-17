<?php

/*
|=============================================================================
| ⚠️  [LEGACY — NOT CONNECTED]
|=============================================================================
|
| File ini adalah sisa dari arsitektur MONOLITIK Livewire.
| Seluruh logika bisnis di sini sudah DIMIGRASI ke:
|
|   Backend API  : App\Http\Controllers\Api\Admin\StaffController
|   Requests     : App\Http\Requests\Api\Admin\{Store,Update}{Staff,Shift}Request
|   Resources    : App\Http\Resources\Api\Admin\{Staff,Shift}Resource
|   Routes       : routes/api.php (prefix: /api/v1/admin/staff & /shifts)
|
| File ini TIDAK boleh didaftarkan ke routes/web.php.
| Simpan hanya sebagai referensi historis (dokumentasi migrasi).
|=============================================================================
*/

namespace App\Livewire\Admin;

use App\Models\StaffShift;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Manajemen Staf & Jadwal Shift — [LEGACY]')]
class StaffManager extends Component
{
    use WithPagination;

    // =========================================================================
    // TAB AKTIF
    // =========================================================================

    /** Tab yang aktif: 'staff' atau 'shifts' */
    public string $activeTab = 'staff';

    // =========================================================================
    // PROPERTI — FORM STAF (REQ-FUNC-039)
    // =========================================================================

    public ?int  $staffId   = null; // null = mode create, isi = mode edit
    public string $name      = '';
    public string $email     = '';
    public string $password  = '';
    public string $role      = 'kasir';
    public bool   $is_active = true;

    /** Kontrol visibilitas modal form staf */
    public bool $showStaffForm = false;

    // =========================================================================
    // PROPERTI — FORM SHIFT (REQ-FUNC-040)
    // =========================================================================

    public ?int  $shiftId    = null; // null = mode create, isi = mode edit
    public int   $userId     = 0;
    public string $shift_date = '';
    public string $start_time = '';
    public string $end_time   = '';
    public string $position   = 'kasir';
    public string $notes      = '';

    /** Kontrol visibilitas modal form shift */
    public bool $showShiftForm = false;

    /** Filter tanggal untuk daftar shift */
    public string $shiftDateFilter = '';

    // =========================================================================
    // PROPERTI — KONFIRMASI HAPUS
    // =========================================================================

    public bool    $confirmingDelete = false;
    public ?int    $deletingId       = null;
    public string  $deletingType     = ''; // 'staff' | 'shift'
    public string  $deletingLabel    = '';

    // =========================================================================
    // PENCARIAN STAF
    // =========================================================================

    public string $staffSearch = '';

    public function updatedStaffSearch(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // ATURAN VALIDASI (server-side — NFR-SC)
    // =========================================================================

    /**
     * Aturan validasi untuk form staf.
     * Email UNIQUE dengan pengecualian untuk data yang sedang diedit.
     */
    protected function staffRules(): array
    {
        return [
            'name'      => ['required', 'string', 'min:2', 'max:100'],
            'email'     => [
                'required', 'email',
                // Abaikan email milik staf yang sedang diedit
                Rule::unique('users', 'email')->ignore($this->staffId),
            ],
            'role'      => ['required', Rule::in(['kasir', 'kds', 'inventory'])],
            'is_active' => ['boolean'],
            // Password hanya wajib saat membuat staf baru
            'password'  => $this->staffId ? ['nullable', 'min:8'] : ['required', 'min:8'],
        ];
    }

    /**
     * Aturan validasi untuk form shift.
     */
    protected function shiftRules(): array
    {
        return [
            'userId'     => ['required', 'exists:users,id'],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
            'position'   => ['required', Rule::in(['kasir', 'inventory', 'dapur'])],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];
    }

    // =========================================================================
    // CRUD — STAF (REQ-FUNC-039)
    // =========================================================================

    /** Buka modal untuk menambah staf baru (reset semua field) */
    public function openCreateStaff(): void
    {
        $this->resetStaffForm();
        $this->showStaffForm = true;
    }

    /** Buka modal untuk mengedit data staf yang sudah ada */
    public function openEditStaff(int $id): void
    {
        // Ambil data staf dari database menggunakan Eloquent (anti SQL injection)
        $staff = User::findOrFail($id);

        $this->staffId   = $staff->id;
        $this->name      = $staff->name;
        $this->email     = $staff->email;
        $this->role      = $staff->role;
        $this->is_active = $staff->is_active;
        $this->password  = ''; // Password tidak pernah ditampilkan kembali

        $this->showStaffForm = true;
    }

    /**
     * Simpan data staf (create atau update).
     * Skenario BDD P9.1: Staf baru berhasil ditambahkan dan credential-nya valid.
     */
    public function saveStaff(): void
    {
        // Validasi server-side (NFR-SC)
        $validated = $this->validate($this->staffRules());

        if ($this->staffId) {
            // ── MODE EDIT ──────────────────────────────────────────────────
            $staff = User::findOrFail($this->staffId);

            $updateData = [
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'role'      => $validated['role'],
                'is_active' => $validated['is_active'],
            ];

            // Hanya update password jika field diisi
            if (! empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $staff->update($updateData);

            session()->flash('status', "Akun staf \"{$staff->name}\" berhasil diperbarui.");
        } else {
            // ── MODE CREATE ────────────────────────────────────────────────
            // P9.1: Buat user baru dengan role staf dan credential yang valid
            User::create([
                'name'              => $validated['name'],
                'email'             => $validated['email'],
                'password'          => Hash::make($validated['password']),
                'role'              => $validated['role'],
                'is_active'         => $validated['is_active'],
                'email_verified_at' => now(), // Staf dibuat oleh owner = langsung terverifikasi
            ]);

            session()->flash('status', "Akun staf \"{$validated['name']}\" berhasil dibuat.");
        }

        // Tutup modal tanpa full page reload (Livewire 4 DOM morphing)
        $this->showStaffForm = false;
        $this->resetStaffForm();
        $this->resetPage();
    }

    /**
     * Toggle status aktif/nonaktif staf secara langsung dari daftar.
     * N9.1: Owner mengubah flag is_active → staf ditolak middleware saat login.
     */
    public function toggleActive(int $id): void
    {
        $staff = User::findOrFail($id);

        // Cegah owner menonaktifkan dirinya sendiri
        if ($staff->id === auth()->id()) {
            session()->flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
            return;
        }

        // Toggle nilai is_active
        $staff->update(['is_active' => ! $staff->is_active]);

        $status = $staff->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';
        session()->flash('status', "Akun \"{$staff->name}\" berhasil {$status}.");
    }

    // =========================================================================
    // CRUD — SHIFT (REQ-FUNC-040)
    // =========================================================================

    /** Buka modal untuk menambah shift baru */
    public function openCreateShift(): void
    {
        $this->resetShiftForm();
        // Default tanggal ke hari ini untuk kemudahan input
        $this->shift_date = now()->toDateString();
        $this->showShiftForm = true;
    }

    /** Buka modal untuk mengedit shift yang ada */
    public function openEditShift(int $id): void
    {
        $shift = StaffShift::findOrFail($id);

        $this->shiftId    = $shift->id;
        $this->userId     = $shift->user_id;
        $this->shift_date = $shift->shift_date->toDateString();
        $this->start_time = substr($shift->start_time, 0, 5); // Format H:i
        $this->end_time   = substr($shift->end_time, 0, 5);
        $this->position   = $shift->position;
        $this->notes      = $shift->notes ?? '';

        $this->showShiftForm = true;
    }

    /**
     * Simpan data shift (create atau update).
     * Skenario BDD P9.3: Shift tersimpan di staff_shifts dan dapat dirender.
     */
    public function saveShift(): void
    {
        // Validasi server-side (NFR-SC)
        $validated = $this->validate($this->shiftRules());

        $data = [
            'user_id'    => $validated['userId'],
            'shift_date' => $validated['shift_date'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'],
            'position'   => $validated['position'],
            'notes'      => $validated['notes'] ?? null,
        ];

        if ($this->shiftId) {
            // Update shift yang sudah ada
            StaffShift::findOrFail($this->shiftId)->update($data);
            session()->flash('status', 'Jadwal shift berhasil diperbarui.');
        } else {
            // P9.3: Simpan shift baru ke tabel staff_shifts
            StaffShift::create($data);
            session()->flash('status', 'Jadwal shift baru berhasil ditambahkan.');
        }

        // Tutup modal tanpa full page reload
        $this->showShiftForm = false;
        $this->resetShiftForm();
    }

    // =========================================================================
    // HAPUS (DENGAN KONFIRMASI)
    // =========================================================================

    /** Tampilkan dialog konfirmasi hapus */
    public function confirmDelete(int $id, string $type): void
    {
        $this->deletingId   = $id;
        $this->deletingType = $type;

        // Ambil label nama untuk konfirmasi yang informatif
        if ($type === 'staff') {
            $this->deletingLabel = User::findOrFail($id)->name;
        } else {
            $shift = StaffShift::with('user')->findOrFail($id);
            $this->deletingLabel = "{$shift->user->name} ({$shift->shift_date->format('d/m/Y')})";
        }

        $this->confirmingDelete = true;
    }

    /** Eksekusi penghapusan setelah konfirmasi */
    public function executeDelete(): void
    {
        if ($this->deletingType === 'staff') {
            $staff = User::findOrFail($this->deletingId);

            // Cegah owner menghapus dirinya sendiri
            if ($staff->id === auth()->id()) {
                session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
                $this->confirmingDelete = false;
                return;
            }

            $name = $staff->name;
            $staff->delete();
            session()->flash('status', "Akun staf \"{$name}\" berhasil dihapus.");
        } elseif ($this->deletingType === 'shift') {
            StaffShift::findOrFail($this->deletingId)->delete();
            session()->flash('status', 'Jadwal shift berhasil dihapus.');
        }

        // Reset state konfirmasi
        $this->confirmingDelete = false;
        $this->deletingId       = null;
        $this->deletingType     = '';
        $this->deletingLabel    = '';
    }

    /** Batalkan dialog konfirmasi hapus */
    public function cancelDelete(): void
    {
        $this->confirmingDelete = false;
        $this->deletingId       = null;
        $this->deletingType     = '';
    }

    // =========================================================================
    // RESET HELPER
    // =========================================================================

    private function resetStaffForm(): void
    {
        $this->reset([
            'staffId', 'name', 'email', 'password', 'role',
        ]);
        $this->is_active = true; // Default staf baru adalah aktif
        $this->resetValidation();
    }

    private function resetShiftForm(): void
    {
        $this->reset([
            'shiftId', 'userId', 'shift_date', 'start_time',
            'end_time', 'position', 'notes',
        ]);
        $this->position = 'kasir'; // Default posisi
        $this->resetValidation();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    /**
     * [LEGACY — DISABLED]
     *
     * Method render() DINONAKTIFKAN.
     * Komponen ini tidak boleh di-render dari route mana pun.
     * Logika yang ada di sini sudah dipindah ke:
     * App\Http\Controllers\Api\Admin\StaffController::index() & shiftIndex()
     *
     * @throws \RuntimeException
     */
    public function render()
    {
        throw new \RuntimeException(
            '[LEGACY] StaffManager Livewire component sudah dinonaktifkan. '
            . 'Gunakan REST API endpoint: GET /api/v1/admin/staff'
        );
    }
}
