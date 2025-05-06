<?php

use Botble\Base\Models\BaseModel;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    public function up(): void
    {
        rescue(function (): void {
            if (setting('real_estate_enable_recaptcha_in_register_page')) {
                DB::table('settings')->insertOrIgnore([
                    'id' => BaseModel::isUsingIntegerId() ? null : (new BaseModel())->newUniqueId(),
                    'key' => 'enable_recaptcha_botble_real_estate_forms_fronts_auth_register_form',
                    'value' => '1',
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });
    }
};
