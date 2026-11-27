<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'publish_at')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // The moment the draft of the page goes live by itself (made-cms:publish-scheduled looks for it every
            // minute), who planned it, and the moment of a planned publication a strip stopped. Nothing is planned
            // for a page that was there already; the planned publication stays when the editor is removed.
            $table->timestamp('publish_at')->nullable()->after('published_at')->index();
            $table->foreignId('scheduled_by')->nullable()->after('publish_at')->constrained('cms_editors')->nullOnDelete();
            $table->timestamp('publish_failed_at')->nullable()->after('scheduled_by');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('scheduled_by');
            $table->dropIndex(['publish_at']);
            $table->dropColumn(['publish_at', 'publish_failed_at']);
        });
    }
};
