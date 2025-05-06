<?php

namespace Botble\RealEstate\Repositories\Eloquent;

use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\RealEstate\Repositories\Interfaces\ConsultInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Eloquent\Collection;

class ConsultRepository extends RepositoriesAbstract implements ConsultInterface
{
    public function getUnread($select = ['*']): Collection
    {
        $data = $this->model->where('status', ConsultStatusEnum::UNREAD)->select($select)->get();
        $this->resetModel();

        return $data;
    }

    public function countUnread(): int
    {
        $data = $this->model->where('status', ConsultStatusEnum::UNREAD)->count();
        $this->resetModel();

        return $data;
    }
}
