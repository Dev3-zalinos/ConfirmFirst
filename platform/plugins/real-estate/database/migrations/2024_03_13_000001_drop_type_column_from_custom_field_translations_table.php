<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_custom_fields_translations')) {
            return;
        }

        Schema::table('re_custom_fields_translations', function (Blueprint $table): void {
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('re_custom_fields_translations')) {
            return;
        }

        Schema::table('re_packages_translations', function (Blueprint $table): void {
            $table->string('type', 60)->nullable();
        });
    }
};
