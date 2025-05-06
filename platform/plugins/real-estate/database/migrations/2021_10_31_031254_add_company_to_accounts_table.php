<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('re_accounts', 'company')) {
            return;
        }

        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->string('company')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('re_accounts', function (Blueprint $table): void {
            $table->dropColumn('company');
        });
    }
};
