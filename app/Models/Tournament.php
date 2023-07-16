<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property Collection $teams
 */
class Tournament extends Model {
    use HasFactory;

    public function teams(): HasMany {
        return $this->hasMany(Team::class);
    }
}
