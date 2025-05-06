<?php

namespace Botble\RealEstate\Contracts;

use Botble\Support\Repositories\Interfaces\RepositoryInterface;

interface ChunkImportable
{
    public function handle(array $rows): void;

    public function save(array $row): void;

    public function map(array $row): array;

    public function mapRelationships(mixed $row, array $data): array;

    public function getIdsFromString(?string $value, RepositoryInterface $repository, string $column = 'name'): ?array;

    public function getImageURLs(array $images): array;

    public function uploadImageFromURL(?string $url): ?string;
}
