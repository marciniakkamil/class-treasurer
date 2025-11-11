<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'collections';

    protected $fillable = [
        'user_id',
        'name',
        'school_year',
        'description',
        'status',
        'is_active',
    ];

    /**
     * Get the user that owns the Collection
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all the guardians for the Collection
     * @return HasMany
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'collection_id');
    }

    /**
     * Get all the payments for the Collection
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'collection_id');
    }

    /**
     * Get all the expenses for the Collection
     * @return HasMany
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'collection_id');
    }

}
