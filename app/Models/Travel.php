<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Tour;
use Cviebrock\EloquentSluggable\Sluggable;


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
        return $this->hasMany(Tour::class, 'foreign_key', 'local_key');
    }
    
    public function numberOfNights(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes["number_of_days"] - 1
        );
    }    
}
