<x-core::form :url="route('account.credits.add', $account->id)">
    <x-core::form.text-input
        :label="__('Number of credits')"
        name="credits"
        type="number"
        value="0"
        :placeholder="__('Number of credits')"
    />

    <x-core::form.select
        :label="__('Action')"
        name="type"
        :options="Botble\RealEstate\Enums\TransactionTypeEnum::labels()"
        :placeholder="__('Number of credits')"
    />

    <x-core::form.textarea
        :label="__('Description')"
        name="description"
        :placeholder="__('Description')"
    />
</x-core::form>
