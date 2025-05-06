<?php

use Botble\PluginManagement\Services\PluginService;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration
{
    public function up(): void
    {
        rescue(function () {
            app(PluginService::class)->remove('team');
        }, report: false);
    }
};
