<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Signups for the checklist wait here until they are confirmed and on the newsletter list (#25).
     */
    public function up(): void
    {
        Schema::create('checklist_signups', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('first_name', 100);
            $table->string('email', 254)->unique();
            $table->date('wedding_date')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('signed_up_at');
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index(['confirmed_at', 'signed_up_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_signups');
    }
};
