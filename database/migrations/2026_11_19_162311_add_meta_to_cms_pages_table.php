<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'meta_title')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // The whole title in Google as the editor typed it; null gives the name of the page with the site name.
            $table->string('meta_title')->nullable()->after('in_menu');
            // The description under the title in Google; null lets Google take a piece of the page itself.
            $table->text('meta_description')->nullable()->after('meta_title');
            // Whether the sitemap of the site lists the page; a page that was there already stays in it.
            $table->boolean('in_sitemap')->default(true)->after('meta_description');
            // Whether Google may show the page; off writes noindex, while the page stays reachable.
            $table->boolean('indexable')->default(true)->after('in_sitemap');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropColumn(['meta_title', 'meta_description', 'in_sitemap', 'indexable']);
        });
    }
};
