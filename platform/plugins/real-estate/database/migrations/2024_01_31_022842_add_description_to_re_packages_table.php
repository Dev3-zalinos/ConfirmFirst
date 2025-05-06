<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_packages', 'description')) {
            Schema::table('re_packages', function (Blueprint $table): void {
                $table->string('description', 400)->nullable();
            });
        }

        if (! Schema::hasColumn('re_packages_translations', 'description')) {
            Schema::table('re_packages_translations', function (Blueprint $table): void {
                $table->string('description', 400)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('re_packages', function (Blueprint $table): void {
            $table->dropColumn('description');
        });

        Schema::table('re_packages_translations', function (Blueprint $table): void {
            $table->dropColumn('description');
        });
    }
};
