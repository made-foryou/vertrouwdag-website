<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'hidden')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // Whether visitors only reach the page through its address: it stays out of the menu, the sitemap and Google.
            $table->boolean('hidden')->default(false)->after('in_menu');
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropColumn('hidden');
        });
    }
};
