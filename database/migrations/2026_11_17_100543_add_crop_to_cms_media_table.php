<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The crop step of made-cms-package added these columns to its own create migration of cms_media. That migration was
 * already published and run here, so the site adds them itself.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cms_media', function (Blueprint $table): void {
            // The upright size of the original of a photo that was scaled down, the image a crop is cut from; null for
            // a file kept as it came in.
            $table->unsignedInteger('original_width')->nullable()->after('height');
            $table->unsignedInteger('original_height')->nullable()->after('original_width');
            // How the photo was cut from its original: {x, y, width, height, rotation, flip, scale_down}, as the editor
            // saw it (CropArea); null for the whole original, not turned and scaled down.
            $table->json('crop')->nullable()->after('focal_y');
        });
    }

    public function down(): void
    {
        Schema::table('cms_media', function (Blueprint $table): void {
            $table->dropColumn(['original_width', 'original_height', 'crop']);
        });
    }
};
