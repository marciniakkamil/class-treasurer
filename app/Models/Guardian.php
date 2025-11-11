<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guardian extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'guardians';

    protected $fillable = [
        'collection_id',
        'name',
        'child_name',
        'contact',
        'is_active',
    ];

    /**
     * Get the collection that owns the Guardian
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class, 'collection_id');
    }

    /**
     * Get all the payments for the Guardian
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'guardian_id');
    }
}
