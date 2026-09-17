<?php

namespace App\Http\Resources\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * API Resource untuk model StaffShift.
 * Mengonversi data shift ke format JSON yang konsisten untuk mobile client.
 */
class ShiftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'user_id'        => $this->user_id,
            'shift_date'     => $this->shift_date->toDateString(),       // Format: Y-m-d
            'start_time'     => substr($this->start_time, 0, 5),         // Format: H:i
            'end_time'       => substr($this->end_time, 0, 5),           // Format: H:i
            'duration_hours' => $this->durationHours(),                   // Kalkulasi dari model
            'position'       => $this->position,
            'position_label' => $this->positionLabel(),                   // e.g. "🖥️ Kasir"
            'notes'          => $this->notes,
            'created_at'     => $this->created_at->toIso8601String(),

            // Sertakan data staf ringkas jika sudah di-eager-load
            'user'           => $this->whenLoaded('user', fn () => [
                'id'       => $this->user->id,
                'name'     => $this->user->name,
                'initials' => $this->user->initials(),
            ]),
        ];
    }
}
