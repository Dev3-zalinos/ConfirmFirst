<div class="col-lg-4 col-md-6">
    <div class="footer-cl-4">
        @if($config['title'])
            <div class="fw-7 text-white">
                {!! BaseHelper::clean($config['title']) !!}
            </div>
        @endif

        @if($config['subtitle'])
            <p class="mt-12 text-variant-2">{!! BaseHelper::clean($config['subtitle']) !!}</p>
        @endif

        {!! $form->renderForm() !!}
    </div>
</div>
