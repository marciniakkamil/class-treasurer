<?php

declare(strict_types=1);

namespace App\Filters;

final class CollectionFilters
{
    public function __construct(
        public readonly string $name = '',
        public readonly string $schoolYear = '',
        public readonly ?bool $isActive = null,
    ) {}

    public static function fromArray(array $input): self
    {
        $name = isset($input['name']) ? trim((string) $input['name']) : '';
        $schoolYear = isset($input['school_year']) ? trim((string) $input['school_year']) : '';

        $isActive = $input['is_active'] ?? null;
        $isActiveNorm = null;
        if ($isActive !== null && $isActive !== '') {
            $bool = filter_var($isActive, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE);
            $isActiveNorm = $bool;
        }

        return new self(
            name: $name,
            schoolYear: $schoolYear,
            isActive: $isActiveNorm,
        );
    }

    /**
     * Export for testing.
     *
     * @return array{name: string, school_year: string, is_active: ?bool}
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'school_year' => $this->schoolYear,
            'is_active' => $this->isActive,
        ];
    }
}
