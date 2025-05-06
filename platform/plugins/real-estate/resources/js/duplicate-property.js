'use strict'

$(() => {
    $(document).on('click', '[data-bb-toggle="duplicate-property"]', function (event) {
        event.preventDefault()

        $httpClient.make()
            .withButtonLoading($(this))
            .post($(this).data('url'))
            .then(({ data }) => {
                Botble.showSuccess(data.message)
                setTimeout(function () {
                    window.location.href = data.data.url
                }, 500)
            })
    })
})
