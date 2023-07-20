<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id;
 */
class Game extends Model {
    use HasFactory;

    const TYPE_DIVISION = 'division';

    const TYPE_PLAYOFFS = 'playoffs';

    const TYPE_SEMI_FINALS = 'semifinals';

    const TYPE_FINALS = 'finals';

    protected $fillable = [
        'tournament_id',
        'type',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function teamGames(): HasMany {
        return $this->hasMany(TeamGame::class);
    }
}
