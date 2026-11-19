<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The sitemaps the package built, the last few of each route group: the database keeps them through a deploy
        // and a cleared cache, and every server of the site serves the same one.
        Schema::create('cms_sitemaps', function (Blueprint $table): void {
            $table->id();
            // The prefix of the route group of Route::madeCms() that serves it, without slashes: "" at the root, "nl".
            $table->string('prefix')->default('');
            $table->longText('xml');
            $table->unsignedInteger('address_count');
            // The pages that belong in it but are left out, each with its path, its name and the reason.
            $table->json('errors');
            // What led to it ("published", "command", ...), with the page or the editor when there is one.
            $table->string('cause', 32);
            $table->foreignId('page_id')->nullable()->constrained('cms_pages')->nullOnDelete();
            $table->foreignId('editor_id')->nullable()->constrained('cms_editors')->nullOnDelete();
            $table->timestamp('built_at');
            $table->index(['prefix', 'built_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_sitemaps');
    }
};
