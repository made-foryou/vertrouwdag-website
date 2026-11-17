<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // For a short while create_cms_media_table made these columns itself; a database that ran it has them already.
        if (Schema::hasColumn('cms_media', 'ulid')) {
            return;
        }

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

        $this->giveEveryFileItsKey();

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
     * Every file the library stored so far lies in a folder of its own, media/{ulid}, so that folder is its key. A file
     * outside such a folder (written by a host itself), or a second file in the same folder, gets a key of its own: it
     * leads to the file all the same, and removing the folder of that key never touches a file of another.
     */
    private function giveEveryFileItsKey(): void
    {
        $taken = [];

        DB::table('cms_media')->select(['id', 'path', 'original_path'])->chunkById(200, function ($files) use (&$taken): void {
            foreach ($files as $file) {
                $folder = preg_match('#^media/([0-9a-z]{26})/#i', $file->original_path ?? $file->path, $match) === 1
                    ? Str::lower($match[1])
                    : null;
                $ulid = $folder === null || isset($taken[$folder]) ? Str::lower((string) Str::ulid()) : $folder;
                $taken[$ulid] = true;

                DB::table('cms_media')->where('id', $file->id)->update(['ulid' => $ulid]);
            }
        });
    }
};
