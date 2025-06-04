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
            // $table->id(); // Primary key

            // $table->foreignId('race_id')->constrained('races')->onDelete('cascade');
            // $table->string('title');
            // $table->foreignId('pollster_id')->constrained('pollsters')->onDelete('cascade');
            // $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');

            // $table->date('field_date_start');
            // $table->date('field_date_end');
            // $table->date('release_date');

            // $table->integer('sample_size');
            // $table->float('margin_of_error');

            // $table->text('source_url');
            // $table->text('tags'); // Can be cast as JSON in the model
            // $table->integer('status')->default(1);
            // $table->timestamps(); // created_at and updated_at

    
                $table->id();
    
                // No foreign key to races or pollsters—just a plain "race" and "pollster" column:
                $table->string('candidate_name');
                $table->string('party')->nullable();
                $table->enum('race', ['primary', 'general', 'midterm', 'approval']);
                $table->decimal('support_percentage', 5, 2);
                $table->enum('approval_rating', ['Approve', 'Disapprove', 'Neutral']);
                $table->string('pollster'); // plain text field
                $table->foreignId('state_id')->nullable()->constrained('states')->onDelete('set null');
                $table->date('field_date_start')->nullable();
                $table->date('field_date_end')->nullable();
                $table->date('release_date')->nullable();
    
                $table->integer('sample_size')->nullable();
                $table->float('margin_of_error')->nullable();
    
                $table->text('source_url')->nullable();
                $table->text('tags')->nullable(); // store as comma-separated string or JSON text
    
                $table->integer('status')->default(1);
    
                $table->timestamps();
    




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
