<?php

namespace Botble\RealEstate\Commands;

use Botble\RealEstate\Enums\ModerationStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Models\Account;
use Botble\RealEstate\Models\AccountActivityLog;
use Botble\RealEstate\Models\Property;
use Botble\RealEstate\Repositories\Interfaces\PropertyInterface;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Support\Facades\DB;

#[AsCommand('cms:properties:renew', 'Renew properties')]
class RenewPropertiesCommand extends Command
{
    public function __construct(protected PropertyInterface $propertyRepository)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        \Log::info('Property renewal check started at: ' . now());
        
        $this->components->info('Processing properties...');

        DB::beginTransaction();
        try {
            $properties = Property::query()
                ->where('moderation_status', ModerationStatusEnum::APPROVED)
                ->where('author_type', Account::class)
                ->where(function ($query) {
                    $query->where('expire_date', '<=', now()->addMinutes(1))
                          ->orWhereNull('expire_date');
                })
                ->with(['author'])
                ->lockForUpdate()
                ->get();

            foreach ($properties as $property) {
                if (!RealEstateHelper::isEnabledCreditsSystem()) {
                    continue;
                }

                $lockKey = 'property_renewal_lock_' . $property->id;
                
                if (!cache()->has($lockKey)) {
                    try {
                        cache()->put($lockKey, true, 1);
                        
                        if ($property->never_expired) {
                            $creditsNeeded = $property->calculateCreditsNeeded();
                            
                            if ($property->author && $property->author->credits >= $creditsNeeded) {
                                // Deduct credits
                                $property->author->credits -= $creditsNeeded;
                                $property->author->save();
                                
                                // Update property expiration
                                $property->expire_date = now()->addMinutes(3);
                                $property->save();
                                
                                // Log the renewal
                                AccountActivityLog::query()->create([
                                    'action' => 'property_renewed',
                                    'reference_name' => $property->name . ' (-' . $creditsNeeded . ' credits)',
                                    'reference_url' => route('public.account.properties.edit', $property->id),
                                    'account_id' => $property->author_id
                                ]);
                                
                                $this->components->info("Renewed property #{$property->id} and deducted {$creditsNeeded} credits");
                            } else {
                                // Not enough credits, mark as expired
                                $property->moderation_status = ModerationStatusEnum::EXPIRED;
                                $property->save();
                                
                                // Log the expiration
                                AccountActivityLog::query()->create([
                                    'action' => 'property_expired',
                                    'reference_name' => $property->name . ' (insufficient credits)',
                                    'reference_url' => route('public.account.properties.edit', $property->id),
                                    'account_id' => $property->author_id
                                ]);
                                
                                $this->components->info("Property #{$property->id} expired due to insufficient credits");
                            }
                        } else {
                            // For non-never_expired properties, simply mark as expired after 3 minutes
                            $property->moderation_status = ModerationStatusEnum::EXPIRED;
                            $property->save();
                            
                            // Log the expiration
                            AccountActivityLog::query()->create([
                                'action' => 'property_expired',
                                'reference_name' => $property->name,
                                'reference_url' => route('public.account.properties.edit', $property->id),
                                'account_id' => $property->author_id
                            ]);
                            
                            $this->components->info("Property #{$property->id} expired (not set to never expire)");
                        }
                    } finally {
                        cache()->forget($lockKey);
                    }
                }
            }

            DB::commit();
            $this->components->info('All properties processed successfully!');
            
            return self::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->components->error('Error processing properties: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Calculate credits needed based on property price
     */
    protected function calculateCreditsNeeded(float $price): int
    {
        return max(1, (int)ceil($price / 1000000));
    }
}