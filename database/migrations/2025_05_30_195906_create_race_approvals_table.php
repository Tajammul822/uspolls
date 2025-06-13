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
        Schema::create('race_approvals', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('race_id')->constrained('races')->onDelete('cascade');
            $table->string('name');
            $table->date('race_date');
            $table->string('pollster');
            $table->integer('sample_size');
            $table->decimal('approve_rating', 5, 2);
            $table->decimal('disapprove_rating', 5, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('race_approvals');
    }
};
