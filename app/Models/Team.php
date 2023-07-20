<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model {
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'name',
        'division',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function teamGames(): HasMany {
        return $this->hasMany(TeamGame::class);
    }
}
