<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Repositories\Interfaces\CategoryInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Collection;

class CategoryRepository extends RepositoriesAbstract implements CategoryInterface
{
    public function getCategories(array $select, array $orderBy, array $conditions = []): Collection
    {
        $data = $this->model->with('slugable')->select($select);
        if ($conditions) {
            $data = $data->where($conditions);
        }
        foreach ($orderBy as $by => $direction) {
            $data = $data->oldest($by);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
