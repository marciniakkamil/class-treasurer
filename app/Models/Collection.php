<?php

namespace App\Models;

use App\Models\Builders\CollectionBuilder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static CollectionBuilder query()
 * @method CollectionBuilder newQuery()
 * @method CollectionBuilder newModelQuery()
 * @mixin CollectionBuilder
 */

// todo change Collection name to Campaign
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
     * Use custom Eloquent Builder.
     */
    public function newEloquentBuilder($query): CollectionBuilder
    {
        return new CollectionBuilder($query);
    }

    /**
     * Scope: limit collections visible to a given user.
     */
    public function scopeVisibleTo(CollectionBuilder $query, User $user): CollectionBuilder
    {
        return $query->visibleTo($user);
    }

    /**
     * Get the user that owns the Collection
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all the guardians for the Collection
     */
    public function guardians(): HasMany
    {
        return $this->hasMany(Guardian::class, 'collection_id');
    }

    /**
     * Get all the payments for the Collection
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'collection_id');
    }

    /**
     * Get all the expenses for the Collection
     */
    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'collection_id');
    }
}
