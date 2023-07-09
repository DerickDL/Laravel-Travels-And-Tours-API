<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tour;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Travel extends Model
{
    use HasFactory, Sluggable;

    /**
     * Table name
     */
    protected $table = "travels";

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'is_public',
        'description',
        'number_of_days',
    ];

    /**
     * Auto-generate unique slug
     */
    public function sluggable(): array 
    {
        return [
            "slug" => [
                "source" => "name"
            ]
        ];
    }


    /**
     * Get all of the Tours for the Travel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class);
    }
    
    /**
     * Accessor for creating virtual column of number of nights
     */
    protected function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->number_of_days - 1
        );
    }    
    
    
}
