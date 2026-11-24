<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'draft_started_at')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // When the draft that is there now began, so the CMS can tell how long visitors have seen the older text.
            $table->timestamp('draft_started_at')->nullable()->after('draft_strips');
        });

        // The start of a draft that was there already is not known; its last change is the closest there is.
        DB::table('cms_pages')
            ->whereNotNull('draft_strips')
            ->update(['draft_started_at' => DB::raw('updated_at')]);
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropColumn('draft_started_at');
        });
    }
};
