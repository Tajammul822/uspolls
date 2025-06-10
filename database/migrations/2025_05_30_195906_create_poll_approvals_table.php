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
        Schema::create('poll_approvals', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('poll_id')->constrained('polls')->onDelete('cascade');
            $table->string('name');
            $table->date('poll_date');
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
        Schema::dropIfExists('poll_approvals');
    }
};
