<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('re_investors_translations')) {
            Schema::create('re_investors_translations', function (Blueprint $table): void {
                $table->string('lang_code');
                $table->foreignId('re_investors_id');
                $table->string('name')->nullable();

                $table->primary(['lang_code', 're_investors_id'], 're_investors_translations_primary');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('re_investors_translations');
    }
};
