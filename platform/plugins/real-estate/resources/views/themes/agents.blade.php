<h1>{{ __('Agents') }}</h1>
{!! Theme::breadcrumb()->render() !!}

<div class="row">
    @foreach($accounts as $account)
        <div class="box col-lg-3 col-sm-6">
            @include('plugins/real-estate::themes.partials.agents.item', ['account' => $account])
        </div>
    @endforeach
</div>

<br>

{!! $accounts->withQueryString()->links() !!}
