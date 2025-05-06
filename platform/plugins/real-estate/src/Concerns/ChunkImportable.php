<?php

namespace Botble\RealEstate\Concerns;

use Botble\Location\Repositories\Interfaces\CityInterface;
use Botble\Location\Repositories\Interfaces\CountryInterface;
use Botble\Location\Repositories\Interfaces\StateInterface;
use Botble\Media\Facades\RvMedia;
use Botble\RealEstate\Contracts\Typeable;
use Botble\RealEstate\Models\CustomFieldValue;
use Botble\RealEstate\Repositories\Interfaces\AccountInterface;
use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\RealEstate\Repositories\Interfaces\CurrencyInterface;
use Botble\RealEstate\Repositories\Interfaces\FacilityInterface;
use Botble\RealEstate\Repositories\Interfaces\FeatureInterface;
use Botble\RealEstate\Repositories\Interfaces\InvestorInterface;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Mime\MimeTypes;

trait ChunkImportable
{
    use Typeable;

    public function __construct(
        protected Request $request,
        protected CountryInterface $countryRepository,
        protected StateInterface $stateRepository,
        protected CityInterface $cityRepository,
        protected CurrencyInterface $currencyRepository,
        protected AccountInterface $accountRepository,
        protected CategoryInterface $categoryRepository,
        protected FacilityInterface $facilityRepository,
        protected FeatureInterface $featureRepository,
        protected CustomFieldValue $customFieldValue,
        protected InvestorInterface $investorRepository
    ) {
    }

    public function handle(array $rows): void
    {
        foreach ($rows as $row) {
            $this->save($this->map($row));
        }
    }

    public function mapRelationships(mixed $row, array $data): array
    {
        $data['country_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'country'), $this->countryRepository));
        $data['state_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'state'), $this->stateRepository));
        $data['city_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'city'), $this->cityRepository));
        $data['author_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'author_id'), $this->accountRepository));
        $data['currency_id'] = Arr::first($this->getIdsFromString(Arr::get($row, 'currency'), $this->currencyRepository, 'title'));
        $data['categories'] = $this->getIdsFromString(Arr::get($row, 'categories'), $this->categoryRepository);
        $data['features'] = $this->getIdsFromString(Arr::get($row, 'features'), $this->featureRepository);
        $data['price'] = (float) Arr::get($row, 'price');
        $data['number_bedroom'] = (int) Arr::get($row, 'number_bedroom');
        $data['number_bathroom'] = (int) Arr::get($row, 'number_bathroom');
        $data['number_floor'] = (int) Arr::get($row, 'number_floor');
        $data['square'] = (float) Arr::get($row, 'square');

        return $data;
    }

    public function getIdsFromString(?string $value, RepositoryInterface $repository, string $column = 'name'): ?array
    {
        if (! $value) {
            return null;
        }

        $items = $this->stringToArray($value);

        $ids = [];

        foreach ($items as $index => $item) {
            if (is_numeric($item)) {
                $column = 'id';
            }

            $ids[$index] = $repository->getModel()->where($column, $item)->value('id');
        }

        return array_filter($ids);
    }

    public function getImageURLs(array $images): array
    {
        $images = array_values(array_filter($images));

        $limitDownloadImageFormUrl = 5;
        $i = 0;

        foreach ($images as $key => $image) {
            $images[$key] = str_replace(RvMedia::getUploadURL() . '/', '', trim($image));
            $images[$key] = str_replace('storage/', '', ltrim($images[$key], '/'));

            if (Str::contains($images[$key], ['http://', 'https://']) && $i < $limitDownloadImageFormUrl) {
                $images[$key] = $this->uploadImageFromURL($images[$key]);
                $i++;
            }
        }

        return $images;
    }

    public function uploadImageFromURL(?string $url): ?string
    {
        if (empty($url)) {
            return $url;
        }

        $info = pathinfo($url);

        try {
            $contents = file_get_contents($url);
        } catch (Exception) {
            return $url;
        }

        if (empty($contents)) {
            return $url;
        }

        $path = '/tmp';

        File::ensureDirectoryExists($path);

        $path = $path . '/' . $info['basename'];

        file_put_contents($path, $contents);

        $mimeTypeDetection = (new MimeTypes())->getMimeTypes(File::extension($url));

        $mimeType = Arr::first($mimeTypeDetection);

        $fileUpload = new UploadedFile($path, $info['basename'], $mimeType, null, true);

        $result = RvMedia::handleUpload($fileUpload, 0, 'properties');

        File::delete($path);

        if (! $result['error']) {
            $url = $result['data']->url;
        }

        return $url;
    }
}
