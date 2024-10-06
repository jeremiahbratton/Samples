<?php

namespace App\Models;

use App\Observers\TourAvailabilityObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([TourAvailabilityObserver::class])]
class Tour extends Model
{
    use HasFactory;

    /**
     * Rate tables are stored separately from tours for both the sake of normalizing data and to make the concept of a tour having 
     * multiple rate tables possible in the future.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Itinerary items are stored in an intermediate table and reference both a tour and a component
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class);
    }

    protected $guarded = [
        'id',
        'created_at',
        'updated_at'
    ];

    protected function casts(): array
    {
        return [
            'overnight_destinations' => 'array',
            'availability' => 'array',
            'available_months' => 'array',
        ];
    }
}
