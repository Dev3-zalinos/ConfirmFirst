@if($transactions->isNotEmpty())
    <x-core::step :vertical="true">
        @foreach($transactions as $transaction)
            <x-core::step.item @class(['user-action' => $transaction->account_id])>
                <div class="h4 m-0">
                    {!! BaseHelper::clean($transaction->getDescription()) !!}
                </div>
                <div class="text-secondary">{{ $transaction->created_at }}</div>
            </x-core::step.item>
        @endforeach
    </x-core::step>
@else
    <p class="mb-0 text-muted text-center">{{ __('No transactions!') }}</p>
@endif
