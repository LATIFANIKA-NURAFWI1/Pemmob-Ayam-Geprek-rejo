<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Admin\StoreShiftRequest;
use App\Http\Requests\Api\Admin\StoreStaffRequest;
use App\Http\Requests\Api\Admin\UpdateShiftRequest;
use App\Http\Requests\Api\Admin\UpdateStaffRequest;
use App\Http\Resources\Api\Admin\ShiftResource;
use App\Http\Resources\Api\Admin\StaffResource;
use App\Models\StaffShift;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * StaffController — REST API untuk manajemen staf dan jadwal shift.
 *
 * Dikonversi dari: App\Livewire\Admin\StaffManager (401 baris Livewire)
 * menjadi controller API yang mengikuti prinsip Single Responsibility.
 *
 * Endpoint Staf   : GET/POST /api/admin/staff
 *                   GET/PUT/DELETE /api/admin/staff/{id}
 *                   PATCH /api/admin/staff/{id}/toggle-active
 *
 * Endpoint Shift  : GET/POST /api/admin/shifts
 *                   PUT/DELETE /api/admin/shifts/{id}
 *
 * Endpoint Utiliti: GET /api/admin/staff/active-list (untuk dropdown)
 */
class StaffController extends Controller
{
    // =========================================================================
    // CRUD — STAF (REQ-FUNC-039)
    // =========================================================================

    /**
     * GET /api/admin/staff
     *
     * Daftar staf dengan pagination dan pencarian.
     * Hanya menampilkan role: kasir, kds, inventory (bukan owner).
     */
    public function index(Request $request): JsonResponse
    {
        $staffList = User::query()
            ->whereIn('role', ['kasir', 'kds', 'inventory'])
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->where(function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })
            )
            ->orderBy('name')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data'    => StaffResource::collection($staffList->items()),
            'meta'    => [
                'current_page' => $staffList->currentPage(),
                'last_page'    => $staffList->lastPage(),
                'per_page'     => $staffList->perPage(),
                'total'        => $staffList->total(),
            ],
        ]);
    }

    /**
     * GET /api/admin/staff/{staff}
     *
     * Detail satu staf beserta jadwal shift-nya.
     */
    public function show(int $staff): JsonResponse
    {
        $user = User::with('shifts')->findOrFail($staff);

        return response()->json([
            'success' => true,
            'data'    => new StaffResource($user),
        ]);
    }

    /**
     * POST /api/admin/staff
     *
     * Membuat akun staf baru.
     * Skenario BDD P9.1: Staf baru berhasil ditambahkan dengan credential valid.
     */
    public function store(StoreStaffRequest $request): JsonResponse
    {
        $staff = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'is_active'         => $request->boolean('is_active', true),
            'email_verified_at' => now(), // Staf dibuat owner = langsung terverifikasi
        ]);

        return response()->json([
            'success' => true,
            'message' => "Akun staf \"{$staff->name}\" berhasil dibuat.",
            'data'    => new StaffResource($staff),
        ], 201);
    }

    /**
     * PUT /api/admin/staff/{staff}
     *
     * Memperbarui data staf. Password hanya diperbarui jika field dikirim.
     */
    public function update(UpdateStaffRequest $request, int $staff): JsonResponse
    {
        $user = User::findOrFail($staff);

        $updateData = [
            'name'      => $request->name,
            'email'     => $request->email,
            'role'      => $request->role,
            'is_active' => $request->boolean('is_active', $user->is_active),
        ];

        // Hanya hash & update password jika field password dikirim dan tidak kosong
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Akun staf \"{$user->name}\" berhasil diperbarui.",
            'data'    => new StaffResource($user->fresh()),
        ]);
    }

    /**
     * DELETE /api/admin/staff/{staff}
     *
     * Menghapus akun staf. Staf tidak bisa menghapus dirinya sendiri.
     */
    public function destroy(Request $request, int $staff): JsonResponse
    {
        $user = User::findOrFail($staff);

        // Proteksi: owner/admin tidak dapat menghapus akun sendiri
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 403);
        }

        $name = $user->name;
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => "Akun staf \"{$name}\" berhasil dihapus.",
        ]);
    }

    /**
     * PATCH /api/admin/staff/{staff}/toggle-active
     *
     * Toggle status aktif/nonaktif staf tanpa full update.
     * N9.1: Owner mengubah flag is_active → staf ditolak middleware saat login.
     */
    public function toggleActive(Request $request, int $staff): JsonResponse
    {
        $user = User::findOrFail($staff);

        // Proteksi: owner tidak dapat menonaktifkan dirinya sendiri
        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.',
            ], 403);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $user->refresh();

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return response()->json([
            'success'   => true,
            'message'   => "Akun \"{$user->name}\" berhasil {$status}.",
            'is_active' => $user->is_active,
        ]);
    }

    /**
     * GET /api/admin/staff/active-list
     *
     * Daftar staf aktif (tanpa pagination) untuk keperluan dropdown
     * pada form pembuatan/editing jadwal shift.
     */
    public function activeList(): JsonResponse
    {
        $activeStaff = User::whereIn('role', ['kasir', 'kds', 'inventory'])
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return response()->json([
            'success' => true,
            'data'    => $activeStaff->map(fn ($u) => [
                'id'   => $u->id,
                'name' => $u->name,
                'role' => $u->role,
            ]),
        ]);
    }

    // =========================================================================
    // CRUD — SHIFT (REQ-FUNC-040)
    // =========================================================================

    /**
     * GET /api/admin/shifts
     *
     * Daftar jadwal shift dengan filter tanggal opsional.
     * Skenario BDD P9.3: Shift dapat diambil dan dirender di mobile.
     */
    public function shiftIndex(Request $request): JsonResponse
    {
        $shifts = StaffShift::query()
            ->with('user') // Eager loading — hindari N+1
            ->when(
                $request->input('shift_date'),
                fn ($q, $date) => $q->where('shift_date', $date)
            )
            ->orderBy('shift_date', 'desc')
            ->orderBy('start_time')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => ShiftResource::collection($shifts->items()),
            'meta'    => [
                'current_page' => $shifts->currentPage(),
                'last_page'    => $shifts->lastPage(),
                'per_page'     => $shifts->perPage(),
                'total'        => $shifts->total(),
            ],
        ]);
    }

    /**
     * POST /api/admin/shifts
     *
     * Membuat jadwal shift baru.
     * Skenario BDD P9.3: Shift tersimpan di staff_shifts.
     */
    public function shiftStore(StoreShiftRequest $request): JsonResponse
    {
        $shift = StaffShift::create([
            'user_id'    => $request->user_id,
            'shift_date' => $request->shift_date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'position'   => $request->position,
            'notes'      => $request->notes,
        ]);

        $shift->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Jadwal shift baru berhasil ditambahkan.',
            'data'    => new ShiftResource($shift),
        ], 201);
    }

    /**
     * PUT /api/admin/shifts/{shift}
     *
     * Memperbarui jadwal shift yang sudah ada.
     */
    public function shiftUpdate(UpdateShiftRequest $request, int $shift): JsonResponse
    {
        $staffShift = StaffShift::findOrFail($shift);

        $staffShift->update([
            'user_id'    => $request->user_id,
            'shift_date' => $request->shift_date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'position'   => $request->position,
            'notes'      => $request->notes,
        ]);

        $staffShift->load('user');

        return response()->json([
            'success' => true,
            'message' => 'Jadwal shift berhasil diperbarui.',
            'data'    => new ShiftResource($staffShift->fresh()->load('user')),
        ]);
    }

    /**
     * DELETE /api/admin/shifts/{shift}
     *
     * Menghapus jadwal shift.
     */
    public function shiftDestroy(int $shift): JsonResponse
    {
        $staffShift = StaffShift::with('user')->findOrFail($shift);
        $label = "{$staffShift->user->name} ({$staffShift->shift_date->format('d/m/Y')})";

        $staffShift->delete();

        return response()->json([
            'success' => true,
            'message' => "Jadwal shift \"{$label}\" berhasil dihapus.",
        ]);
    }
}
