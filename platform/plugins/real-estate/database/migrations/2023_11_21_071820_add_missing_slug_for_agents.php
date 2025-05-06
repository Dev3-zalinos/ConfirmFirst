<?php

use Botble\RealEstate\Models\Account;
use Botble\Slug\Facades\SlugHelper;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        foreach (Account::query()->get() as $account) {
            /**
             * @var Account $account
             */
            SlugHelper::createSlug($account, $account->username);
        }
    }
};
