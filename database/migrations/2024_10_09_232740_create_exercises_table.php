<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->enum('type', ['time_based', 'distance_based', 'time_distance_based', 'repetition_based']);
            $table->float('duration')->nullable();  // 時間ベース
            $table->float('distance')->nullable();  // 距離ベース
            $table->integer('sets')->nullable();    // 回数ベース（セット数）
            $table->integer('reps')->nullable();    // 回数ベース（レップ数）
            $table->integer('weight')->nullable(); // 重量
            $table->integer('calories')->nullable(); // 消費カロリー
            $table->string('comment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
