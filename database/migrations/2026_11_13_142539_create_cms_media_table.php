<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per file of the media library; strips store its id, so a file changes everywhere it is used.
        Schema::create('cms_media', function (Blueprint $table): void {
            $table->id();
            // A folder keeps its files: deleting one that still holds files fails.
            $table->foreignId('folder_id')->nullable()->constrained('cms_media_folders')->restrictOnDelete();
            $table->string('kind')->index();
            // The slug of the file name without extension, the name search engines read.
            $table->string('name');
            // The file the site shows, on the media disk.
            $table->string('path');
            // The upload as it came in, next to a photo that was scaled down; null when the file was kept as it is.
            $table->string('original_path')->nullable();
            // The small picture of a photo that was scaled down, for the tiles of the CMS; null for any other file.
            $table->string('thumb_path')->nullable();
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            // One alt text for every place the file is used; for a document the description of its download link.
            $table->string('alt')->nullable();
            // The point a crop keeps in view, from 0 to 1 from the left and the top.
            $table->decimal('focal_x', 5, 4)->nullable();
            $table->decimal('focal_y', 5, 4)->nullable();
            // The editor who uploaded the file; the file stays when the editor is removed.
            $table->foreignId('uploaded_by')->nullable()->constrained('cms_editors')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cms_media');
    }
};
