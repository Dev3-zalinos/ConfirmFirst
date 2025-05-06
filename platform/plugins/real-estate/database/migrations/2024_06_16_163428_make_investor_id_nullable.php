<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        rescue(function (): void {
            Schema::table('re_projects', function (Blueprint $table): void {
                $table->foreignId('investor_id')->nullable()->change();
            });
        });
    }

    public function down(): void
    {
        Schema::table('re_projects', function (Blueprint $table): void {
            $table->foreignId('investor_id')->change();
        });
    }
};
