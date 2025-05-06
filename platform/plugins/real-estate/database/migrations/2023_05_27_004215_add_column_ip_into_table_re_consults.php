<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('re_consults', 'ip_address')) {
            return;
        }

        Schema::table('re_consults', function (Blueprint $table): void {
            $table->string('ip_address', 39)->nullable()->after('property_id');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('re_consults', 'ip_address')) {
            return;
        }

        Schema::table('re_consults', function (Blueprint $table): void {
            $table->dropColumn('ip_address');
        });
    }
};
