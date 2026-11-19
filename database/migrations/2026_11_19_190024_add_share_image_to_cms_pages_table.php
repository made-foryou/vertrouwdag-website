<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'share_image_id')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // The image of a shared link to the page. Its place in cms_media_usages keeps the file in the library;
            // the key is the last guard.
            $table->foreignId('share_image_id')->nullable()->after('indexable')->constrained('cms_media')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('share_image_id');
        });
    }
};
