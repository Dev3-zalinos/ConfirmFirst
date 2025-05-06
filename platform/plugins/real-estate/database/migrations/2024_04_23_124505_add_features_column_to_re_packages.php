<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_packages', 'features')) {
            Schema::table('re_packages', function (Blueprint $table): void {
                $table->text('features')->nullable();
            });
        }

        if (! Schema::hasColumn('re_packages_translations', 'features')) {
            Schema::table('re_packages_translations', function (Blueprint $table): void {
                $table->text('features')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('re_packages', 'features')) {
            Schema::table('re_packages', function (Blueprint $table): void {
                $table->dropColumn('features');
            });
        }

        if (Schema::hasColumn('re_packages_translations', 'features')) {
            Schema::table('re_packages_translations', function (Blueprint $table): void {
                $table->dropColumn('features');
            });
        }
    }
};
