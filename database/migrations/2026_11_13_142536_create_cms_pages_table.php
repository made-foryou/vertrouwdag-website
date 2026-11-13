<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cms_pages', function (Blueprint $table): void {
            $table->id();
            // A parent keeps its children: deleting one for good while it still has children fails.
            $table->foreignId('parent_id')->nullable()->constrained('cms_pages')->restrictOnDelete();
            $table->string('title');
            // Not unique: the address is unique in cms_routes, so pages under different parents share a slug.
            $table->string('slug');
            // The place among the pages with the same parent, 0 for the first.
            $table->unsignedInteger('position')->default(0);
            $table->boolean('in_menu')->default(true);
            $table->string('status')->default('draft')->index();
            $table->json('strips');
            $table->json('draft_strips')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['parent_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_pages');
    }
};
