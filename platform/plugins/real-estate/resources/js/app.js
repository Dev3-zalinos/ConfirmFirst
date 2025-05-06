require('./form')

$(() => {
    if (window.noticeMessages) {
        window.noticeMessages.forEach((message) => {
            Botble.showNotice(
                message.type,
                message.message,
                message.type === 'error'
                    ? window.trans && window.trans.error
                        ? window.trans.error
                        : 'Error!'
                    : window.trans && window.trans.success
                    ? window.trans.success
                    : 'Success!'
            )
        })
    }

    $(document).on('click', '[data-bb-toggle="property-renew-modal"]', (event) => {
        event.preventDefault()

        const $currentTarget = $(event.currentTarget)

        $('.button-confirm-renew')
            .data('section', $currentTarget.prop('href'))
            .data('parent-table', $currentTarget.closest('.table').prop('id'))
        $('.modal-confirm-renew').modal('show')
    })

    $('.button-confirm-renew').on('click', (event) => {
        event.preventDefault()
        const $currentTarget = $(event.currentTarget)

        let url = $currentTarget.data('section')

        $currentTarget.addClass('button-loading')

        $httpClient.make()
            .withButtonLoading($currentTarget)
            .post(url)
            .then(({ data }) => {
                window.LaravelDataTables[$currentTarget.data('parent-table')]
                    .row($(`a[data-section="${url}"]`).closest('tr'))
                    .remove()
                    .draw()
                Botble.showSuccess(data.message)
            })
            .finally(() => $currentTarget.closest('.modal').modal('hide'))
    })

    $(document).on('click', '.btn_remove_image', (event) => {
        event.preventDefault()
        $(event.currentTarget).closest('.image-box').find('.preview-image-wrapper').hide()
        $(event.currentTarget).closest('.image-box').find('.image-data').val('')
    })

    const refreshCoupon = (url) => {
        $httpClient.make()
            .get(url)
            .then(({ data }) => {
                $('.order-detail-box').html(data.data)
            })
    }

    $(document)
        .on('click', '.toggle-coupon-form', () => $(document).find('.coupon-form').toggle('fast'))
        .on('click', '.apply-coupon-code', (e) => {
            e.preventDefault()

            const $button = $(e.currentTarget)

            $httpClient.make()
                .withButtonLoading($button)
                .post($button.data('url'), {
                    coupon_code: $button.closest('form').find('input[name="coupon_code"]').val(),
                })
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    const refreshUrl = $('.order-detail-box').data('refresh-url')
                    refreshCoupon(refreshUrl)
                })
        })
        .on('click', '.remove-coupon-code', (e) => {
            e.preventDefault()

            const $button = $(e.currentTarget)

            $httpClient.make()
                .post($button.data('url'))
                .then(({ data }) => {
                    Botble.showSuccess(data.message)

                    const refreshUrl = $('.order-detail-box').data('refresh-url')
                    refreshCoupon(refreshUrl)
                })
        })

    function handleToggleDrawer() {
        $('.navbar-toggler').on('click', function() {
            $('.ps-drawer--mobile').addClass('active')
            $('.ps-site-overlay').addClass('active')
        })

        $('.ps-drawer__close').on('click', function() {
            $('.ps-drawer--mobile').removeClass('active')
            $('.ps-site-overlay').removeClass('active')
        })

        $('body').on('click', function(e) {
            if ($(e.target).siblings('.ps-drawer--mobile').hasClass('active')) {
                $('.ps-drawer--mobile').removeClass('active')
                $('.ps-site-overlay').removeClass('active')
            }
        })
    }

    handleToggleDrawer()
})
