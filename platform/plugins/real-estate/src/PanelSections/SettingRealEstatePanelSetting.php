<?php

namespace Botble\RealEstate\PanelSections;

use Botble\Base\PanelSections\PanelSection;
use Botble\Base\PanelSections\PanelSectionItem;

class SettingRealEstatePanelSetting extends PanelSection
{
    public function setup(): void
    {
        $this
            ->setId('settings.real_estate')
            ->setTitle(trans('plugins/real-estate::real-estate.name'))
            ->withPriority(1000)
            ->addItems([
                PanelSectionItem::make('settings.real-estate.general_settings')
                    ->setTitle(trans('plugins/real-estate::settings.general.name'))
                    ->withIcon('ti ti-settings')
                    ->withDescription(trans('plugins/real-estate::settings.general.description'))
                    ->withPriority(10)
                    ->withRoute('real-estate.settings.general'),
                PanelSectionItem::make('settings.real-estate.currency_settings')
                    ->setTitle(trans('plugins/real-estate::settings.currencies'))
                    ->withIcon('ti ti-coin')
                    ->withPriority(20)
                    ->withDescription(trans('plugins/real-estate::settings.currency.description'))
                    ->withRoute('real-estate.settings.currencies'),
                PanelSectionItem::make('settings.real-estate.account_settings')
                    ->setTitle(trans('plugins/real-estate::settings.account.name'))
                    ->withIcon('ti ti-user-cog')
                    ->withPriority(30)
                    ->withDescription(trans('plugins/real-estate::settings.account.description'))
                    ->withRoute('real-estate.settings.accounts'),
                PanelSectionItem::make('settings.real-estate.invoice_settings')
                    ->setTitle(trans('plugins/real-estate::settings.invoice.name'))
                    ->withIcon('ti ti-file-invoice')
                    ->withPriority(40)
                    ->withDescription(trans('plugins/real-estate::settings.invoice.description'))
                    ->withRoute('real-estate.settings.invoices'),
                PanelSectionItem::make('settings.real-estate.invoice_template_settings')
                    ->setTitle(trans('plugins/real-estate::settings.invoice_template.name'))
                    ->withIcon('ti ti-list-details')
                    ->withDescription(trans('plugins/real-estate::settings.invoice_template.description'))
                    ->withPriority(50)
                    ->withRoute('real-estate.settings.invoice-template'),
            ]);
    }
}
