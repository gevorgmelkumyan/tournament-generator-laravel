<?php

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
                'division',
                'playoffs',
                'semifinals',
                'finals',
            ])->default('division');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('games');
    }
};
