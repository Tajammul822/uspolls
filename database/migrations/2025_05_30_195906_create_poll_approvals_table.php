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
            $table->decimal('approve_percentage', 5, 2);
            $table->decimal('disapprove_percentage', 5, 2);
            $table->decimal('neutral_percentage', 5, 2)->nullable(); 
            $table->string('subject'); 
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
