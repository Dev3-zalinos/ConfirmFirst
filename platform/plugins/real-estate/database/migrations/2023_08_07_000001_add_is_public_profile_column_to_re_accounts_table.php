<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->boolean('is_public_profile')->default(false)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->dropColumn('is_public_profile');
        });
    }
};
