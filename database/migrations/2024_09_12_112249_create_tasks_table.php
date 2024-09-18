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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('place_id')->nullable()->constrained('places')->cascadeOnDelete();
            $table->string('name');
            $table->text('note')->nullable();
            $table->integer('hr');
            $table->integer('min');
            $table->datetime('duedate')->nullable();
            $table->boolean('reminder');
            $table->integer('repeat');

            $table->string('timeframe')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
