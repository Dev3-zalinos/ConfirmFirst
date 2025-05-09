<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('re_projects', 'latitude')) {
            Schema::table('re_projects', function ($table): void {
                $table->string('latitude', 25)->nullable();
                $table->string('longitude', 25)->nullable();
            });
        }

        if (! Schema::hasColumn('re_properties', 'latitude')) {
            Schema::table('re_properties', function ($table): void {
                $table->string('latitude', 25)->nullable();
                $table->string('longitude', 25)->nullable();
            });
        }
    }

    public function down(): void
    {
        Schema::table('re_projects', function ($table): void {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('re_properties', function ($table): void {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
