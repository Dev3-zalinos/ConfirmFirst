<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Tables\UnverifiedAccountTable;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class UnverifiedAccountController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            abort_unless(setting('real_estate_enable_account_verification', false), 404);

            return $next($request);
        });
    }

    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/real-estate::account.unverified_account.name'), route('unverified-accounts.index'));
    }

    public function index(UnverifiedAccountTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::account.unverified_account.name'));

        return $dataTable->renderTable();
    }

    public function show(string $id)
    {
        $account = Account::query()
            ->whereNull('approved_at')
            ->findOrFail($id);

        $this->pageTitle(trans('plugins/real-estate::account.unverified_account.moderate_account', [
            'name' => $account->name,
        ]));

        return view('plugins/real-estate::unverified-accounts.show', compact('account'));
    }

    public function approve(string $id)
    {
        $account = Account::query()
            ->whereNull('approved_at')
            ->findOrFail($id);

        $account->approved_at = Carbon::now();
        $account->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
            ])
            ->sendUsingTemplate('account-approved', $account->email);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::account.unverified_account.approve_success'))
            ->setNextUrl(route('account.edit', $account->getKey()));
    }

    public function reject(string $id, Request $request)
    {
        $request->validate([
            'reason' => ['required', 'string'],
        ]);

        $account = Account::query()
            ->whereNull('approved_at')
            ->findOrFail($id);

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
                'rejection_reason' => $request->input('reason'),
            ])
            ->sendUsingTemplate('account-rejected', $account->email);

        $account->delete();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/real-estate::account.unverified_account.reject_success'))
            ->setNextUrl(route('unverified-accounts.index'));
    }
}
