<?php

namespace Botble\RealEstate\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Botble\RealEstate\Enums\ReviewStatusEnum;
use Botble\RealEstate\Facades\RealEstateHelper;
use Botble\RealEstate\Notifications\ConfirmEmailNotification;
use Botble\RealEstate\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Throwable;

class Account extends BaseModel implements
    AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use HasApiTokens;
    use Notifiable;

    protected $table = 're_accounts';

    protected $fillable = [
        'first_name',
        'last_name',
        'username',
        'email',
        'password',
        'avatar_id',
        'dob',
        'phone',
        'description',
        'gender',
        'company',
        'country_id',
        'state_id',
        'city_id',
        'is_featured',
        'is_public_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'datetime',
        'package_start_date' => 'datetime',
        'package_end_date' => 'datetime',
        'is_featured' => 'boolean',
        'is_public_profile' => 'boolean',
        'first_name' => SafeContent::class,
        'last_name' => SafeContent::class,
        'username' => SafeContent::class,
        'phone' => SafeContent::class,
        'description' => SafeContent::class,
        'company' => SafeContent::class,
        'password' => 'hashed',
        'approved_at' => 'datetime',
    ];

    public function activityLogs(): HasMany
    {
        return $this->hasMany(AccountActivityLog::class, 'account_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (Account $account): void {
            $account->activityLogs()->delete();
            $account->transactions()->delete();
            $account->reviews()->delete();
            $account->packages()->detach();

            $folder = Storage::path($account->upload_folder);
            if (File::isDirectory($folder) && Str::endsWith($account->upload_folder, '/' . $account->username)) {
                File::deleteDirectory($folder);
            }

            $account->reviews()->delete();
            $account->invoices()->delete();
        });
    }

    public static function generateUsername(string $firstName, ?string $lastName = null): string
    {
        $i = 0;

        do {
            $username = Str::slug($firstName . ' ' . $lastName) . ($i ?: '');
            $i++;
        } while (static::query()->where('username', $username)->exists());

        return $username;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new ConfirmEmailNotification());
    }

    public function avatar(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class)->withDefault();
    }

    protected function firstName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
            set: fn ($value) => ucfirst($value),
        );
    }

    protected function lastName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => ucfirst($value),
            set: fn ($value) => ucfirst($value),
        );
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->first_name . ' ' . $this->last_name,
        );
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar->url) {
                return RvMedia::url($this->avatar->url);
            }

            if ($defaultAvatar = setting('real_estate_account_default_avatar')) {
                return RvMedia::getImageUrl($defaultAvatar);
            }

            try {
                return Avatar::createBase64Image($this->name);
            } catch (Throwable) {
                return RvMedia::getDefaultImage();
            }
        });
    }

    /**
     * @deprecated since v2.22
     */
    protected function fullName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->name
        );
    }

    protected function credits(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (! RealEstateHelper::isEnabledCreditsSystem()) {
                    return 0;
                }

                return $value ?: 0;
            }
        );
    }

    public function properties(): MorphMany
    {
        return $this->morphMany(Property::class, 'author');
    }

    public function projects(): MorphMany
    {
        return $this->morphMany(Project::class, 'author');
    }

    public function canPost(): bool
    {
        return ! RealEstateHelper::isEnabledCreditsSystem() || $this->credits > 0;
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id');
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class, 're_account_packages', 'account_id', 'package_id');
    }

    protected function uploadFolder(): Attribute
    {
        return Attribute::make(
            get: function () {
                $folder = $this->username ? 'accounts/' . $this->username : 'accounts';

                return apply_filters('real_estate_account_upload_folder', $folder, $this);
            }
        );
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function canReview(Project|Property $model): bool
    {
        if (! auth('account')->check()) {
            return false;
        }

        return ! $model
            ->reviews()
            ->whereNot('status', ReviewStatusEnum::REJECTED)
            ->where('account_id', auth('account')->id())
            ->exists();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
