<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_properties_translations', function (Blueprint $table): void {
            $table->longText('floor_plans')->nullable()->after('location');
        });

        Schema::table('re_projects_translations', function (Blueprint $table): void {
            $table->longText('floor_plans')->nullable()->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('re_properties_translations', function (Blueprint $table): void {
            $table->dropColumn('floor_plans');
        });

        Schema::table('re_projects_translations', function (Blueprint $table): void {
            $table->dropColumn('floor_plans');
        });
    }
};
