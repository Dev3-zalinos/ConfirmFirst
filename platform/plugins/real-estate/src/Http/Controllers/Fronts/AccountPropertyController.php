<?php

namespace Botble\RealEstate\Http\Controllers\Fronts;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HiddenField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Rules\MediaImageRule;
use Botble\Media\Facades\RvMedia;
use Botble\Optimize\Facades\OptimizerHelper;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Enums\PropertyStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\AccountPropertyForm;
use Botble\RealEstate\Http\Requests\AccountPropertyRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\SavePropertyCustomFieldService;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Tables\AccountPropertyTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AccountPropertyController extends BaseController
{
    public function __construct()
    {
        OptimizerHelper::disable();
    }

    public function index(AccountPropertyTable $propertyTable)
    {
        $this->pageTitle(trans('plugins/real-estate::account-property.properties'));

        return $propertyTable->render('plugins/real-estate::account.table.base');
    }

    public function create()
    {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        $this->pageTitle(trans('plugins/real-estate::account-property.write_property'));

        return AccountPropertyForm::create()
            ->disablePermalinkField(! setting('allow_customizing_post_url', true))
            ->add('is_slug_editable', HiddenField::class, TextFieldOption::make()->value(1))
            ->renderForm();
    }

    public function store(
        AccountPropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService
    ) {
        if (! auth('account')->user()->canPost()) {
            return redirect()->back()->with(['error_msg' => trans('plugins/real-estate::package.add_credit_alert')]);
        }

        // Check credits before processing
        if (RealEstateHelper::isEnabledCreditsSystem()) {
            $account = Account::query()->findOrFail(auth('account')->id());
            $creditsNeeded = $this->calculateCreditsNeeded((float) $request->input('price', 0));
            
            if ($account->credits < $creditsNeeded) {
                return redirect()->back()->with([
                    'error_msg' => trans('plugins/real-estate::property.not_enough_credits', [
                        'credits' => $creditsNeeded,
                    ]),
                ]);
            }
        }

        $request->merge(['floor_plans' => $this->uploadFloorPlans($request)]);

        $propertyForm = AccountPropertyForm::create()->setRequest($request);

        $propertyForm->saving(function (AccountPropertyForm $form) use (
            $propertyCategoryService,
            $saveFacilitiesService,
            $savePropertyCustomFieldService
        ): void {
            $request = $form->getRequest();

            /**
             * @var Property $property
             */
            $property = $form->getModel();

            $property->fill(array_merge($this->processRequestData($request), [
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ]));

            $property->expire_date = Carbon::now()->addMinutes(3);

            $enabledPostApproval = (bool) setting('enable_post_approval', 1);

            if (! $enabledPostApproval && $property->status != PropertyStatusEnum::DRAFT) {
                $property->moderation_status = ModerationStatusEnum::APPROVED;
            } else {
                $property->moderation_status = ModerationStatusEnum::PENDING;
            }

            $property->save();

            if (RealEstateHelper::isEnabledCustomFields()) {
                $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
            }

            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));

            $propertyCategoryService->execute($request, $property);

            $form->fireModelEvents($property);

            AccountActivityLog::query()->create([
                'action' => 'create_property',
                'reference_name' => $property->name,
                'reference_url' => route('public.account.properties.edit', $property->id),
            ]);

            if (RealEstateHelper::isEnabledCreditsSystem()) {
                $account = Account::query()->findOrFail(auth('account')->id());
                // Calculate credits based on property price
                $creditsNeeded = $this->calculateCreditsNeeded((float) $property->price);
                $account->credits -= $creditsNeeded;
                $account->save();
            }

            if ($enabledPostApproval && $property->status != PropertyStatusEnum::DRAFT) {
                EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'post_name' => $property->name,
                        'post_url' => route('property.edit', $property->id),
                        'post_author' => $property->author->name,
                    ])
                    ->sendUsingTemplate('new-pending-property');
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.properties.index'))
            ->setNextUrl(route('public.account.properties.edit', $propertyForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id)
    {
        $property = Property::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        $this->pageTitle(trans('plugins/real-estate::property.edit') . ' "' . $property->name . '"');

        return AccountPropertyForm::createFromModel($property)
            ->disablePermalinkField($isDisabledPermalinkField = ! setting('allow_customizing_post_url', true))
            ->when($isDisabledPermalinkField, function (AccountPropertyForm $form): AccountPropertyForm {
                return $form
                    ->modify(
                        'name',
                        TextField::class,
                        NameFieldOption::make()
                            ->required()
                            ->helperText($form->getModel()->url . '?preview=true'),
                        true
                    );
            })
            ->renderForm();
    }

    public function update(
        int|string $id,
        AccountPropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService
    ) {
        $property = Property::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();
            
        $oldPrice = $property->price;
        $newPrice = (float) $request->input('price', 0);
        
        // Check if credits are needed for price increase
        if (RealEstateHelper::isEnabledCreditsSystem() && $newPrice > $oldPrice) {
            $account = Account::query()->findOrFail(auth('account')->id());
            $oldCredits = $this->calculateCreditsNeeded((float) $oldPrice);
            $newCredits = $this->calculateCreditsNeeded((float) $newPrice);
            $creditDifference = $newCredits - $oldCredits;
            
            if ($creditDifference > 0 && $account->credits < $creditDifference) {
                return redirect()->back()->with([
                    'error_msg' => trans('plugins/real-estate::property.not_enough_credits_for_price_increase', [
                        'credits' => $creditDifference,
                    ]),
                ]);
            }
        }
        
        $request->merge(['floor_plans' => $this->uploadFloorPlans($request)]);

        $propertyForm = AccountPropertyForm::createFromModel($property)->setRequest($request);

        $propertyForm->saving(function (AccountPropertyForm $form) use (
            $propertyCategoryService,
            $saveFacilitiesService,
            $savePropertyCustomFieldService,
            $oldPrice
        ): void {
            $request = $form->getRequest();

            /**
             * @var Property $property
             */
            $property = $form->getModel();

            $property->fill($this->processRequestData($request));

            // Always set the moderation status to pending when property is updated
            if ($property->status != PropertyStatusEnum::DRAFT) {
                $property->moderation_status = ModerationStatusEnum::PENDING;
            }
            
            // Handle credit adjustments for price changes if enabled
            if (RealEstateHelper::isEnabledCreditsSystem() && $oldPrice != $property->price) {
                $oldCredits = $this->calculateCreditsNeeded((float) $oldPrice);
                $newCredits = $this->calculateCreditsNeeded((float) $property->price);
                $creditDifference = $newCredits - $oldCredits;
                
                if ($creditDifference != 0) {
                    $account = Account::query()->find($property->author_id);
                    
                    if ($account) {
                        if ($creditDifference > 0) {
                            // Price increased, deduct additional credits
                            $account->credits -= $creditDifference;
                            $account->save();
                        } else {
                            // Price decreased, refund credits
                            $account->credits -= $creditDifference; // Will add credits since difference is negative
                            $account->save();
                        }
                    }
                }
            }

            $property->save();

            $form->fireModelEvents($property);

            if (RealEstateHelper::isEnabledCustomFields()) {
                $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
            }

            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));

            $propertyCategoryService->execute($request, $property);

            AccountActivityLog::query()->create([
                'action' => 'update_property',
                'reference_name' => $property->name,
                'reference_url' => route('public.account.properties.edit', $property->id),
            ]);

            // Keep the existing email notification logic
            $enabledPostApproval = (bool) setting('enable_post_approval', 1);
            if ($enabledPostApproval && $property->status != PropertyStatusEnum::DRAFT) {
                EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
                    ->setVariableValues([
                        'post_name' => $property->name,
                        'post_url' => route('property.edit', $property->id),
                        'post_author' => $property->author->name,
                    ])
                    ->sendUsingTemplate('new-pending-property');
            }
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.properties.index'))
            ->setNextUrl(route('public.account.properties.edit', $property->id))
            ->withUpdatedSuccessMessage();
    }

    protected function processRequestData(Request $request): array
    {
        $shortcodeCompiler = shortcode()->getCompiler();

        $request->merge([
            'content' => $shortcodeCompiler->strip($request->input('content'), $shortcodeCompiler->whitelistShortcodes()),
        ]);

        $except = [
            'is_featured',
            'author_id',
            'author_type',
            'expire_date',
            'never_expired',
            'moderation_status',
        ];

        foreach ($except as $item) {
            $request->request->remove($item);
        }

        return $request->input();
    }

    public function destroy(int|string $id)
    {
        $property = Property::query()
            ->where([
                'id' => $id,
                'author_id' => auth('account')->id(),
                'author_type' => Account::class,
            ])
            ->firstOrFail();

        AccountActivityLog::query()->create([
            'action' => 'delete_property',
            'reference_name' => $property->name,
        ]);

        return DeleteResourceAction::make($property);
    }

    public function renew(int|string $id)
    {
        $property = Property::query()->findOrFail($id);

        $account = auth('account')->user();

        $creditsNeeded = $property->calculateCreditsNeeded((float) $property->price);

        if (RealEstateHelper::isEnabledCreditsSystem() && $account->credits < $creditsNeeded) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__("You don't have enough credits to renew this property! {$creditsNeeded} credits required."));
        }

        $property->expire_date = Carbon::now()->addMinutes(3);
        $property->moderation_status = ModerationStatusEnum::APPROVED;
        $property->save();

        if (RealEstateHelper::isEnabledCreditsSystem()) {
            $account->credits -= $creditsNeeded;
            $account->save();
        }

        return $this
            ->httpResponse()
            ->setMessage(__('Renew property successfully'));
    }

    protected function uploadFloorPlans(AccountPropertyRequest $request)
    {
        $imageRules = [];

        foreach ($request->allFiles() as $key => $file) {
            if (! str_starts_with($key, 'floor_plans___')) {
                continue;
            }

            $imageRules[$key] = ['nullable', new MediaImageRule()];
        }

        if ($imageRules) {
            $request->validate($imageRules);
        }

        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $uploadFolder = $account->upload_folder;

        $floorPlans = $request->input('floor_plans');

        foreach ($request->allFiles() as $key => $file) {
            if (! str_starts_with($key, 'floor_plans___')) {
                continue;
            }

            $result = RvMedia::handleUpload($file, 0, $uploadFolder);

            if (! $result['error']) {
                $key = str_replace('floor_plans___', '', $key);
                $key = str_replace('_input', '', $key);
                $key = str_replace('___', '.', $key);

                Arr::set($floorPlans, $key, $result['data']->url);
            }
        }

        return $floorPlans;
    }

    /**
     * Calculate credits needed based on property price
     * 
     * @param float $price Property price
     * @return int Number of credits to deduct
     */
    protected function calculateCreditsNeeded(float $price): int
    {
        // If price is less than or equal to 1 million, deduct 1 credit
        if ($price <= 1000000) {
            return 1;
        }
        
        // For prices above 1 million, calculate how many million-dollar segments
        // Each million-dollar segment (or part thereof) costs 1 credit
        return (int) ceil($price / 1000000);
    }
}