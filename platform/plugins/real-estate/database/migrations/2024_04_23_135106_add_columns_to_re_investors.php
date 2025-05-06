<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_investors', 'description')) {
            Schema::table('re_investors', function (Blueprint $table): void {
                $table->string('description', 400)->nullable();
                $table->string('avatar')->nullable();
            });
        }

        if (! Schema::hasColumn('re_investors_translations', 'description')) {
            Schema::table('re_investors_translations', function (Blueprint $table): void {
                $table->string('description', 400)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('re_investors', 'description')) {
            Schema::table('re_investors', function (Blueprint $table): void {
                $table->dropColumn('description');
                $table->dropColumn('avatar');
            });
        }

        if (Schema::hasColumn('re_investors_translations', 'description')) {
            Schema::table('re_investors_translations', function (Blueprint $table): void {
                $table->dropColumn('description');
            });
        }
    }
};
