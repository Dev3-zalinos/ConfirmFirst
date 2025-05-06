<div class="modal fade" id="modalLogin">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {!! $loginForm->renderForm() !!}
        </div>
    </div>
</div>

@if (RealEstateHelper::isRegisterEnabled())
    <div class="modal modal-lg fade" id="modalRegister">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {!! $registerForm->renderForm() !!}
            </div>
        </div>
    </div>
@endif
