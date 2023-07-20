<?php

use App\Models\Game;
use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tournament::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('type', [
                Game::TYPE_DIVISION,
                Game::TYPE_PLAYOFFS,
                Game::TYPE_SEMI_FINALS,
                Game::TYPE_FINALS,
            ])->default(Game::TYPE_DIVISION);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('games');
    }
};
