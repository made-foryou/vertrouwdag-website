<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_media', 'variants')) {
            return;
        }

        Schema::table('cms_media', function (Blueprint $table): void {
            // The narrower sizes of a photo for the srcset of the site, [{path, width, height}] from narrow to wide;
            // null while they were not made (a file from before them, or no photo), [] for a photo that needs none.
            // The files that are there get theirs from made-cms:media-variants.
            $table->json('variants')->nullable()->after('thumb_path');
        });
    }

    public function down(): void
    {
        Schema::table('cms_media', function (Blueprint $table): void {
            $table->dropColumn('variants');
        });
    }
};
