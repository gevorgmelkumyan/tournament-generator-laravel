<?php

use App\Models\Tournament;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Tournament::class)
                ->constrained()
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->string('name');
            $table->enum('division', ['A', 'B']);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('teams');
    }
};
