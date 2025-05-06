<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\ACL\Traits\RegistersUsers;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\Fronts\Auth\RegisterForm;
use Botble\RealEstate\Http\Requests\Fronts\Auth\RegisterRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Notifications\ConfirmEmailNotification;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

class RegisterController extends BaseController
{
    use RegistersUsers;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->redirectTo = route('public.account.dashboard');
    }

    public function showRegistrationForm()
    {
        abort_unless(RealEstateHelper::isRegisterEnabled(), 404);

        SeoHelper::setTitle(__('Register'));

        return Theme::scope(
            'real-estate.account.auth.register',
            ['form' => RegisterForm::create()],
            'plugins/real-estate::themes.auth.register'
        )->render();
    }

    public function confirm(int|string $id, Request $request)
    {
        abort_unless(RealEstateHelper::isRegisterEnabled(), 404);

        abort_unless(URL::hasValidSignature($request), 404);

        /**
         * @var Account $account
         */
        $account = Account::query()->findOrFail($id);

        $account->confirmed_at = Carbon::now();
        $account->save();

        $this->guard()->login($account);

        return $this
            ->httpResponse()
            ->setNextUrl(route('public.account.dashboard'))
            ->setMessage(__('You successfully confirmed your email address.'));
    }

    protected function guard()
    {
        return auth('account');
    }

    public function resendConfirmation(Request $request)
    {
        abort_unless(RealEstateHelper::isRegisterEnabled(), 404);

        /**
         * @var Account $account
         */
        $account = Account::query()->where('email', $request->input('email'))->first();

        if (! $account) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Cannot find this account!'));
        }

        $this->sendConfirmationToUser($account);

        return $this
            ->httpResponse()
            ->setMessage(__('We sent you another confirmation email. You should receive it shortly.'));
    }

    protected function sendConfirmationToUser(Account $account)
    {
        $account->notify(app(ConfirmEmailNotification::class));
    }

    public function register(RegisterRequest $request)
    {
        abort_unless(RealEstateHelper::isRegisterEnabled(), 404);

        if (! $request->has('username')) {
            $request->merge(['username' => Account::generateUsername(
                $request->input('first_name'),
                $request->input('last_name')
            )]);
        }

        /**
         * @var Account $account
         */
        $account = $this->create($request->input());

        event(new Registered($account));

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'account_name' => $account->name,
                'account_email' => $account->email,
            ])
            ->sendUsingTemplate('account-registered');

        if (setting('verify_account_email', false)) {
            $this->sendConfirmationToUser($account);

            $this->registered($request, $account);

            $message = __('We have sent you an email to verify your email. Please check and confirm your email address!');

            return $this
                ->httpResponse()
                ->setNextUrl(route('public.account.login'))
                ->with(['auth_warning_message' => $message])
                ->setMessage($message);
        }

        $account->confirmed_at = Carbon::now();

        $account->is_public_profile = false;

        $account->save();

        $this->guard()->login($account);

        return $this
            ->httpResponse()
            ->setNextUrl($this->redirectPath())->setMessage(__('Registered successfully!'));
    }

    protected function create(array $data)
    {
        return Account::query()->forceCreate([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'] ?? null,
            'username' => $data['username'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);
    }

    public function getVerify()
    {
        abort_unless(RealEstateHelper::isRegisterEnabled(), 404);

        return view('plugins/real-estate::account.auth.verify');
    }
}
