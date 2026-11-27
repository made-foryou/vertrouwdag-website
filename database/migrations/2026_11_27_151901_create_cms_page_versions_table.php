<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Every publication of a page: the strips that went live, so an editor can put an older version back as the
        // draft. Only the strips, because the rest of a page (name, address, meta) is saved at once and never published.
        Schema::create('cms_page_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('page_id')->constrained('cms_pages')->cascadeOnDelete();
            // 1 for the first publication the page keeps, counting on per page; pruning never renumbers.
            $table->unsignedInteger('number');
            $table->json('strips');
            // What the publication changed compared to the strips that were live before, as facts: the screen makes the
            // sentence in the language of the CMS when it shows it.
            $table->json('changes');
            $table->foreignId('published_by')->nullable()->constrained('cms_editors')->nullOnDelete();
            $table->timestamp('published_at');
            $table->unique(['page_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_page_versions');
    }
};
