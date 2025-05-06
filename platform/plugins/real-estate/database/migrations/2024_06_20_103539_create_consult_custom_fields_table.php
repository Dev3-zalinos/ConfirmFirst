<?php

use Botble\Base\Enums\BaseStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('re_consult_custom_fields', function (Blueprint $table): void {
            $table->id();
            $table->string('type');
            $table->boolean('required')->default(false);
            $table->string('name');
            $table->string('placeholder')->nullable();
            $table->integer('order')->default(999);
            $table->string('status')->default(BaseStatusEnum::PUBLISHED);
            $table->timestamps();
        });

        Schema::create('re_consult_custom_field_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('custom_field_id');
            $table->string('label')->nullable();
            $table->string('value');
            $table->integer('order')->default(999);
            $table->timestamps();
        });

        Schema::create('re_consult_custom_fields_translations', function (Blueprint $table): void {
            $table->foreignId('re_consult_custom_fields_id');
            $table->string('lang_code');
            $table->string('name')->nullable();
            $table->string('placeholder')->nullable();

            $table->primary(['lang_code', 're_consult_custom_fields_id']);
        });

        Schema::create('re_consult_custom_field_options_translations', function (Blueprint $table): void {
            $table->foreignId('re_consult_custom_field_options_id');
            $table->string('lang_code');
            $table->string('label')->nullable();
            $table->string('value')->nullable();

            $table->primary(['lang_code', 're_consult_custom_field_options_id']);
        });

        Schema::table('re_consults', function (Blueprint $table): void {
            $table->text('custom_fields')->nullable()->after('content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('re_consult_custom_fields');
        Schema::dropIfExists('re_consult_custom_field_options');
        Schema::dropIfExists('re_consult_custom_fields_translations');
        Schema::dropIfExists('re_consult_custom_field_options_translations');

        Schema::table('re_consults', function (Blueprint $table): void {
            $table->dropColumn('custom_fields');
        });
    }
};
