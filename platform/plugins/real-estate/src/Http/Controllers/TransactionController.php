<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Enums\TransactionTypeEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Http\Requests\CreateTransactionRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\Transaction;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\TransactionInterface;
use Illuminate\Support\Facades\Auth;

class TransactionController extends BaseController
{
    public function __construct(
        protected TransactionInterface $transactionRepository,
        protected AccountInterface $accountRepository
    ) {
    }

    public function postCreate(int|string $id, CreateTransactionRequest $request)
    {
        abort_unless(RealEstateHelper::isEnabledCreditsSystem(), 404);

        $account = Account::query()->findOrFail($id);

        $request->merge([
            'user_id' => Auth::user()->getKey(),
            'account_id' => $id,
        ]);

        Transaction::query()->create($request->input());

        if ($request->input('type') == TransactionTypeEnum::ADD) {
            $account->credits += $request->input('credits');
        } elseif ($request->input('type') == TransactionTypeEnum::REMOVE) {
            $credits = $account->credits - $request->input('credits');
            $account->credits = max($credits, 0);
        }

        $account->save();

        return $this
            ->httpResponse()
            ->withCreatedSuccessMessage();
    }
}
