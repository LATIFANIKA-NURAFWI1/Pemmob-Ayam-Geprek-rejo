<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource untuk model User (dalam konteks staf).
 *
 * Mengontrol field mana yang diekpos ke response JSON — password
 * dan data sensitif lain TIDAK pernah dimasukkan di sini.
 */
class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'role'       => $this->role,
            'is_active'  => $this->is_active,
            'initials'   => $this->initials(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // Sertakan jadwal shift jika sudah di-eager-load
            'shifts'     => ShiftResource::collection($this->whenLoaded('shifts')),
        ];
    }
}
