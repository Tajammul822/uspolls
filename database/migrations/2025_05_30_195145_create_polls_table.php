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
        Schema::create('polls', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('race_id')->constrained('races')->onDelete('cascade');
            $table->string('title');
            $table->foreignId('pollster_id')->constrained('pollsters')->onDelete('cascade');
            $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');

            $table->date('field_date_start');
            $table->date('field_date_end');
            $table->date('release_date');

            $table->integer('sample_size');
            $table->float('margin_of_error');

            $table->text('source_url');
            $table->text('tags'); // Can be cast as JSON in the model
            $table->integer('status')->default(1);

            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polls');
    }
};
