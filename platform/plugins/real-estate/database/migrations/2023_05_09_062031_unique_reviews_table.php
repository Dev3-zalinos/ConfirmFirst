<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        $tablePrefix = Schema::getConnection()->getTablePrefix();
        $tmpTable = "{$tablePrefix}re_reviews_tmp";
        $table = "{$tablePrefix}re_reviews";

        DB::statement("CREATE TABLE IF NOT EXISTS $tmpTable LIKE $table");
        DB::statement("TRUNCATE TABLE $tmpTable");
        DB::statement("INSERT $tmpTable SELECT * FROM $table");
        DB::statement("TRUNCATE TABLE $table");

        Schema::table('re_reviews', function (Blueprint $table): void {
            $table->unique(['account_id', 'reviewable_id', 'reviewable_type'], 'reviews_unique');
        });

        DB::table('re_reviews_tmp')->oldest()->chunk(1000, function ($chunked): void {
            DB::table('re_reviews')->insertOrIgnore(array_map(fn ($item) => (array) $item, $chunked->toArray()));
        });

        Schema::dropIfExists('re_reviews_tmp');
    }

    public function down(): void
    {
        Schema::table('re_reviews', function (Blueprint $table): void {
            $table->dropUnique('reviews_unique');
        });
    }
};
