<?php

use App\Models\Game;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('team_games', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Game::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Team::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedTinyInteger('score');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('team_games');
    }
};
