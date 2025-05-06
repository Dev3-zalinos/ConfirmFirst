<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_projects', function (Blueprint $table) {
            $table->integer('featured_priority')->default(0)->nullable()->change();
        });

        Schema::table('re_properties', function (Blueprint $table) {
            $table->integer('featured_priority')->default(0)->nullable()->change();
        });
    }
};
