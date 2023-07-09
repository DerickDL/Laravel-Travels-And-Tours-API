<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Travel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    use HasFactory;

    protected $fillable = [
        'travel_id',
        'name',
        'starting_date',
        'ending_date',
        'price',
    ];

    /**
     * Get the Travel that owns the Tour
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function travels(): BelongsTo
    {
        return $this->belongsTo(Travel::class);
    }

    /**
     * Price accessor and mutator
     * Multiply by 100 when saving
     * Divide by 100 when acessing
     */
    public function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value / 100,
            set: fn ($value) => $value * 100,
        );
    }

}
