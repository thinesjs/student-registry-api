<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request, $type = 'all'): array
    {
        return [
            // 'id' => $this->id,
            'name' => $this->name,
            // 'email' => $this->email,
            'address' => $this->address,
            // 'assigned_course' => $this->assigned_course,
            // 'created_at' => $this->created_at->format('d/m/Y'),
            // 'updated_at' => $this->updated_at->format('d/m/Y'),
        ];
    }
}
