<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('cities', 'slug')) {
            Schema::table('cities', function (Blueprint $table): void {
                $table->string('slug', 120)->unique()->after('name')->nullable();
            });
        }

        if (! Schema::hasColumn('cities', 'image')) {
            Schema::table('cities', function (Blueprint $table): void {
                $table->string('image')->after('order')->nullable();
            });
        }

        if (! Schema::hasColumn('states', 'slug')) {
            Schema::table('states', function (Blueprint $table): void {
                $table->string('slug', 120)->unique()->after('name')->nullable();
            });
        }

        if (! Schema::hasColumn('states', 'image')) {
            Schema::table('states', function (Blueprint $table): void {
                $table->string('image')->after('order')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('cities', 'image')) {
            Schema::table('cities', function (Blueprint $table): void {
                $table->dropColumn('image');
            });
        }

        if (Schema::hasColumn('cities', 'slug')) {
            Schema::table('cities', function (Blueprint $table): void {
                $table->dropColumn('slug');
            });
        }

        if (Schema::hasColumn('states', 'image')) {
            Schema::table('states', function (Blueprint $table): void {
                $table->dropColumn('image');
            });
        }

        if (Schema::hasColumn('states', 'slug')) {
            Schema::table('states', function (Blueprint $table): void {
                $table->dropColumn('slug');
            });
        }
    }
};
