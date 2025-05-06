<?php

namespace Botble\RealEstate\Forms;

use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\FormAbstract;
use Botble\RealEstate\Enums\ConsultStatusEnum;
use Botble\RealEstate\Http\Requests\ConsultRequest;
use Botble\RealEstate\Models\Consult;

class ConsultForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Consult::class)
            ->setValidatorClass(ConsultRequest::class)
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(ConsultStatusEnum::labels())->toArray())
            ->addMetaBoxes([
                'information' => [
                    'title' => trans('plugins/real-estate::consult.consult_information'),
                    'content' => view('plugins/real-estate::info', ['consult' => $this->getModel()])->render(),
                ],
            ])
            ->setBreakFieldPoint('status');
    }
}
