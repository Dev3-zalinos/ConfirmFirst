<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Forms\PropertyForm;
use Botble\RealEstate\Http\Requests\PropertyRequest;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Services\SaveFacilitiesService;
use Botble\RealEstate\Services\SavePropertyCustomFieldService;
use Botble\RealEstate\Services\StorePropertyCategoryService;
use Botble\RealEstate\Tables\PropertyTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class PropertyController extends BaseController
{
    public function __construct()
    {
        parent::__construct();

        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::property.name'), route('property.index'));
    }

    public function index(PropertyTable $dataTable)
    {
        $this->pageTitle(trans('plugins/real-estate::property.name'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/real-estate::property.create'));

        return PropertyForm::create()->renderForm();
    }

    public function store(
        PropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService
    ) {
        $request->merge([
            'expire_date' => Carbon::now()->addDays(RealEstateHelper::propertyExpiredDays()),
            'images' => array_filter($request->input('images', [])),
            'author_type' => Account::class,
        ]);

        $propertyForm = PropertyForm::create()->setRequest($request);

        $propertyForm->saving(function (PropertyForm $form) use ($propertyCategoryService, $saveFacilitiesService, $savePropertyCustomFieldService): void {
            $request = $form->getRequest();

            /**
             * @var Property $property
             */
            $property = $form->getModel();
            $property->fill($request->input());
            $property->moderation_status = ModerationStatusEnum::APPROVED;
            $property->never_expired = $request->input('never_expired');
            $property->save();

            $form->fireModelEvents($property);

            if (RealEstateHelper::isEnabledCustomFields()) {
                $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
            }

            $property->features()->sync($request->input('features', []));

            $saveFacilitiesService->execute($property, $request->input('facilities', []));
            $propertyCategoryService->execute($request, $property);
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $propertyForm->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(int|string $id)
    {
        /**
         * @var Property $property
         */
        $property = Property::query()->with(['features', 'author'])->findOrFail($id);

        Assets::addScriptsDirectly(['vendor/core/plugins/real-estate/js/duplicate-property.js']);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $property->name]));

        return PropertyForm::createFromModel($property)->renderForm();
    }

    public function update(
        int|string $id,
        PropertyRequest $request,
        StorePropertyCategoryService $propertyCategoryService,
        SaveFacilitiesService $saveFacilitiesService,
        SavePropertyCustomFieldService $savePropertyCustomFieldService
    ) {
        $property = Property::query()->findOrFail($id);
        $oldPrice = $property->price;
        $newPrice = (float) $request->input('price', 0);
        
        // Check for credit availability if this is a user property
        if (RealEstateHelper::isEnabledCreditsSystem() && 
            $property->author_id && 
            $property->author_type === Account::class &&
            $newPrice > $oldPrice) {
            
            $author = Account::query()->find($property->author_id);
            
            if ($author) {
                $oldCredits = $this->calculateCreditsNeeded((float) $oldPrice);
                $newCredits = $this->calculateCreditsNeeded((float) $newPrice);
                $creditDifference = $newCredits - $oldCredits;
                
                if ($creditDifference > 0 && $author->credits < $creditDifference) {
                    return $this
                        ->httpResponse()
                        ->setError()
                        ->setMessage(trans('plugins/real-estate::property.not_enough_credits_for_price_increase', [
                            'credits' => $creditDifference,
                        ]));
                }
            }
        }

        return PropertyForm::createFromModel($property)
            ->setRequest($request)
            ->saving(function (PropertyForm $form) use ($propertyCategoryService, $saveFacilitiesService, $savePropertyCustomFieldService, $oldPrice): void {
                $request = $form->getRequest();

                /**
                 * @var Property $property
                 */
                $property = $form->getModel();
                
                // Check if property is being approved
                $wasApproved = $property->moderation_status !== ModerationStatusEnum::APPROVED &&
                              $request->input('moderation_status') === ModerationStatusEnum::APPROVED;
                
                // Preserve never_expired and moderation_status
                $neverExpired = $request->input('never_expired', $property->never_expired);
                $moderationStatus = $request->input('moderation_status', $property->moderation_status);
                
                $property->fill($request->except(['expire_date', 'never_expired', 'moderation_status']));
                $property->author_type = Account::class;
                $property->images = array_filter($request->input('images', []));
                
                // Set never_expired and moderation_status
                $property->never_expired = $neverExpired;
                $property->moderation_status = $moderationStatus;
                
                // Only set expiration if not never expired and status is approved
                if (!$property->never_expired && $property->moderation_status === ModerationStatusEnum::APPROVED) {
                    $property->expire_date = Carbon::now()->addMinutes(3);
                }
                
                // Handle credit adjustments for price changes if enabled
                if (RealEstateHelper::isEnabledCreditsSystem() && 
                    $property->author_id && 
                    $property->author_type === Account::class && 
                    $oldPrice != $property->price) {
                    
                    $oldCredits = $this->calculateCreditsNeeded((float) $oldPrice);
                    $newCredits = $this->calculateCreditsNeeded((float) $property->price);
                    $creditDifference = $newCredits - $oldCredits;
                    
                    if ($creditDifference != 0) {
                        $author = Account::query()->find($property->author_id);
                        
                        if ($author) {
                            if ($creditDifference > 0) {
                                // Price increased, deduct additional credits
                                $author->credits -= $creditDifference;
                                $author->save();
                            } else {
                                // Price decreased, refund credits
                                $author->credits -= $creditDifference; // Will add credits since difference is negative
                                $author->save();
                            }
                        }
                    }
                }
                
                $property->save();

                // Create activity log if property was just approved
                if ($wasApproved) {
                    AccountActivityLog::query()->create([
                        'action' => 'property_approved',
                        'reference_name' => $property->name,
                        'reference_url' => route('public.account.properties.edit', $property->id),
                        'account_id' => $property->author_id
                    ]);
                }

                $form->fireModelEvents($property);

                if (RealEstateHelper::isEnabledCustomFields()) {
                    $savePropertyCustomFieldService->execute($property, $request->input('custom_fields', []));
                }

                $property->features()->sync($request->input('features', []));

                $saveFacilitiesService->execute($property, $request->input('facilities', []));
                $propertyCategoryService->execute($request, $property);
            });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setNextUrl(route('property.edit', $property->getKey()))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Property $property)
    {
        return DeleteResourceAction::make($property);
    }

    public function approve(int|string $id)
    {
        $property = Property::query()->findOrFail($id);
        
        DB::beginTransaction();
        try {
            $property->moderation_status = ModerationStatusEnum::APPROVED;
            $property->expire_date = Carbon::now()->addMinutes(3);
            
            // Only deduct credits if never_expired and credits weren't already deducted
            if ($property->never_expired && 
                RealEstateHelper::isEnabledCreditsSystem() && 
                !AccountActivityLog::query()
                    ->where('action', 'property_approved_with_credits')
                    ->where('reference_url', 'LIKE', '%' . $property->id . '%')
                    ->exists()) {
                
                $creditsNeeded = $property->calculateCreditsNeeded();
                
                if ($property->author && $property->author->credits >= $creditsNeeded) {
                    // Deduct initial credits
                    $property->author->credits -= $creditsNeeded;
                    $property->author->save();
                    
                    // Log the credit deduction with user-friendly message
                    AccountActivityLog::query()->create([
                        'action' => 'property_approved_with_credits',
                        'reference_name' => trans('plugins/real-estate::property.actions.property_approved_with_credits', [
                            'name' => $property->name,
                            'credits' => $creditsNeeded
                        ]),
                        'reference_url' => route('public.account.properties.edit', $property->id),
                        'account_id' => $property->author_id
                    ]);
                } else {
                    throw new Exception(trans('plugins/real-estate::property.not_enough_credits'));
                }
            }
            
            $property->save();
            
            // Create approval activity log with user-friendly message
            AccountActivityLog::query()->create([
                'action' => 'property_approved',
                'reference_name' => trans('plugins/real-estate::property.actions.property_approved', [
                    'name' => $property->name
                ]),
                'reference_url' => route('public.account.properties.edit', $property->id),
                'account_id' => $property->author_id
            ]);
            
            DB::commit();
            
            return response()->json([
                'message' => trans('plugins/real-estate::property.approved_success'),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function reject(Property $property, Request $request)
    {
        abort_unless($property->is_pending_moderation, 404);

        $request->validate([
            'reason' => ['required', 'string', 'max:400'],
        ]);

        $property->moderation_status = ModerationStatusEnum::REJECTED;
        $property->reject_reason = $request->input('reason');
        $property->save();

        EmailHandler::setModule(REAL_ESTATE_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'author_name' => $property->author->name,
                'property_name' => $property->name,
                'property_link' => route('public.account.properties.edit', $property->getKey()),
                'reason' => $request->input('reason'),
            ])
            ->sendUsingTemplate('property-rejected', $property->author->email);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('property.index'))
            ->setMessage(trans('plugins/real-estate::property.status_moderation.rejected'));
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