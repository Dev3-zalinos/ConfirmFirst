<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_properties', function (Blueprint $table): void {
            $table->string('reject_reason', 400)->after('moderation_status')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('re_properties', function (Blueprint $table): void {
            $table->dropColumn('reject_reason');
        });
    }
};
