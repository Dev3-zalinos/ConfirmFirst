<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_accounts_translations')) {
            Schema::create('re_accounts_translations', function (Blueprint $table): void {
                $table->string('lang_code');
                $table->foreignId('re_accounts_id');
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('description')->nullable();

                $table->primary(['lang_code', 're_accounts_id'], 're_accounts_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_accounts_translations');
    }
};
