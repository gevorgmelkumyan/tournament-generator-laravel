<?php

use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('division_games', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tournament::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Team::class, 'team_1_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignIdFor(Team::class, 'team_2_id')
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedSmallInteger('team_1_score');
            $table->unsignedSmallInteger('team_2_score');


            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('division_games');
    }
};
