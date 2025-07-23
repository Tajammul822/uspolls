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
        Schema::create('races', function (Blueprint $table) {
            $table->id();
            $table->enum('race', ['election', 'approval']);
            $table->enum('race_type', ['president', 'senate', 'house', 'governor', 'mayor', 'generic ballot', 'other'])->nullable();
            $table->enum('election_round', ['primary', 'general'])->nullable();
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');
            $table->integer('district')->nullable();
            $table->unsignedInteger('is_featured')->nullable()->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('races');
    }
};
