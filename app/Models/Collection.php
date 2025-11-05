<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

//    public function parents()
//    {
//        return $this->hasMany(ParentModel::class);
//    }
//
//    public function payments()
//    {
//        return $this->hasMany(Payment::class);
//    }
//
//    public function expenses()
//    {
//        return $this->hasMany(Expense::class);
//    }

}
