<?php

use Botble\Setting\Facades\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('re_consults', function (Blueprint $table): void {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable()->change();
        });

        Setting::set('real_estate_mandatory_fields_at_consult_form', json_encode(['email']));
        Setting::save();
    }

    public function down(): void
    {
        Schema::table('re_consults', function (Blueprint $table): void {
            $table->string('email')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();
        });
    }
};
