<?php

use Botble\Base\Facades\BaseHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        try {
            Schema::table('re_facilities_distances', function (Blueprint $table): void {
                $table->dropPrimary('re_facilities_distances_primary');
            });

            Schema::table('re_facilities_distances', function (Blueprint $table): void {
                $table->primary(['facility_id', 'reference_id', 'reference_type'], 'facilities_distances_primary');
            });
        } catch (Throwable $exception) {
            BaseHelper::logError($exception);
        }
    }
};
