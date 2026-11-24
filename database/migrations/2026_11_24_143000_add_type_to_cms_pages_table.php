<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'type')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // The key of the page type the site defines (PageType); empty for an ordinary page, as every page that
            // was there already is. A key the site no longer defines stays.
            $table->string('type', 100)->nullable()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropColumn('type');
        });
    }
};
