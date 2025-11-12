<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Collection */
class CollectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => (string) $this->name,
            'school_year' => $this->school_year,
            'description' => $this->description,
            'status' => $this->status,
            'is_active' => (bool) $this->is_active,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
        ];

        $include = collect((array) $request->query('include'));
        $shouldIncludeAggregates = $include->contains('aggregates');

        if ($shouldIncludeAggregates) {
            $data['aggregates'] = [
                'guardians_count' => $this->when(isset($this->guardians_count), fn () => (int) $this->guardians_count),
                'payments_count' => $this->when(isset($this->payments_count), fn () => (int) $this->payments_count),
                'expenses_count' => $this->when(isset($this->expenses_count), fn () => (int) $this->expenses_count),
                'payments_sum_amount' => $this->when(isset($this->payments_sum_amount), fn () => (float) $this->payments_sum_amount),
                'expenses_sum_amount' => $this->when(isset($this->expenses_sum_amount), fn () => (float) $this->expenses_sum_amount),
            ];
        }

        return $data;
    }
}
