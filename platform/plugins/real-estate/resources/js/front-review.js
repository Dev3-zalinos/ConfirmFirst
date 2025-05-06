$(() => {
    new StarRating('.star-rating')
    const $reviewList = $(document).find('.reviews-list')

    const fetchReviews = (url) => {
        $reviewList.append('<div class="loading-spinner"></div>')

        $.get(url || `${$reviewList.data('url')}`, ({ data }) => {
            $reviewList.html(data)

            if (typeof Theme.lazyLoadInstance !== 'undefined') {
                Theme.lazyLoadInstance.update()
            }
        })
    }

    fetchReviews()

    $(document)
        .on('submit', '.review-form', (e) => {
            e.preventDefault()

            const $form = $(e.currentTarget)
            const $button = $form.find('button[type="submit"]')

            $.ajax({
                method: 'POST',
                url: $form.prop('action'),
                data: $form.serialize(),
                beforeSend: () => $button.prop('disabled', true).addClass('btn-loading'),
                success: ({ data }) => {
                    $form.get(0).reset()
                    $form.find('textarea').prop('disabled', true).val('')
                    Theme.showSuccess(data.message)
                    fetchReviews()
                },
                error: (response) => {
                    Theme.handleError(response)
                    $button.prop('disabled', false)
                },
                complete: () => {
                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha()
                    }

                    $button.removeClass('btn-loading')
                },
            })
        })
        .on('click', '.pagination ul li a', (e) => {
            e.preventDefault()

            fetchReviews(e.target.href)

            $('html, body').animate({ scrollTop: $reviewList.offset().top - 220 }, 0)
        })
})
