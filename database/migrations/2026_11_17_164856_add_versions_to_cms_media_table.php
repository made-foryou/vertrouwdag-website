<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replacing and restoring files in the media library of made-cms-package added these columns to its own create
 * migration of cms_media. That migration was already published and run here, so the site adds them itself.
 *
 * The ulid is the folder every file already has on the media disk, media/{ulid}, so it is read from the path of each
 * existing file. The paths are checked before the table changes: MySQL does not roll back a schema change.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ulids = $this->ulidsFromPaths();

        Schema::table('cms_media', function (Blueprint $table): void {
            // The folder of the file on the media disk, media/{ulid}, which holds every version of it; also the key of
            // the link that always leads to the file as it is now, so it never changes.
            $table->char('ulid', 26)->nullable()->after('id');
            // The number of the version the site shows: 1 for the upload, each replacement the highest so far plus one.
            $table->unsignedInteger('version')->default(1)->after('crop');
            // The version that waits to be put back (MediaVersion): the files and columns the file had before it was
            // replaced, or before the version it had was put back; null when nothing waits.
            $table->json('previous')->nullable()->after('version');
            // Who placed the version the site shows, and when, by replacing the file; null for the upload itself.
            $table->foreignId('replaced_by')->nullable()->after('uploaded_by')->constrained('cms_editors')->nullOnDelete();
            $table->timestamp('replaced_at')->nullable()->after('replaced_by');
        });

        foreach ($ulids as $id => $ulid) {
            DB::table('cms_media')->where('id', $id)->update(['ulid' => $ulid]);
        }

        Schema::table('cms_media', function (Blueprint $table): void {
            $table->char('ulid', 26)->nullable(false)->change();
            $table->unique('ulid');
        });
    }

    public function down(): void
    {
        Schema::table('cms_media', function (Blueprint $table): void {
            $table->dropUnique(['ulid']);
            $table->dropConstrainedForeignId('replaced_by');
            $table->dropColumn(['ulid', 'version', 'previous', 'replaced_at']);
        });
    }

    /**
     * Reads the folder of each file from its path, and refuses a path outside media/{ulid}.
     *
     * @return array<int, string>
     */
    private function ulidsFromPaths(): array
    {
        $ulids = [];

        foreach (DB::table('cms_media')->orderBy('id')->get(['id', 'path']) as $media) {
            if (preg_match('#^media/([0-9a-z]{26})/#', $media->path, $matches) !== 1) {
                throw new RuntimeException("cms_media {$media->id} has no folder media/{ulid} in its path: {$media->path}");
            }

            $ulids[$media->id] = $matches[1];
        }

        return $ulids;
    }
};
