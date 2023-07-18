<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
