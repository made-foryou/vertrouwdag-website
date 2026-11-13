<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per address of the site, pointing at the content that lives there.
        Schema::create('cms_routes', function (Blueprint $table): void {
            $table->id();
            $table->string('path')->unique();
            $table->morphs('routable');
            $table->unique(['routable_type', 'routable_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_routes');
    }
};
