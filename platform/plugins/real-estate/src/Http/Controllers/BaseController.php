<?php

namespace Botble\RealEstate\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController as Controller;

abstract class BaseController extends Controller
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/real-estate::real-estate.name'));
    }
}
