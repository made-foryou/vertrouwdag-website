<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('cms_pages', 'updated_by')) {
            return;
        }

        Schema::table('cms_pages', function (Blueprint $table): void {
            // Who changed the page last, next to when (updated_at). Who changed a page that was there already is not
            // known, so it stays empty; the page stays when the editor is removed.
            $table->foreignId('updated_by')->nullable()->after('updated_at')->constrained('cms_editors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('cms_pages', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('updated_by');
        });
    }
};
