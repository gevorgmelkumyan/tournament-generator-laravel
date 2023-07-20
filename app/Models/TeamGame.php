<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamGame extends Model {
    use HasFactory;

    protected $fillable = [
        'game_id',
        'team_id',
        'score',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function game(): BelongsTo {
        return $this->belongsTo(Game::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }
}
