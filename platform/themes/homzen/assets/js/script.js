'use strict'

$(() => {
    window.Theme = window.Theme || {}

    window.Theme.isRtl = () => {
        return document.body.getAttribute('dir') === 'rtl'
    }

    const setCookie = (name, value, days) => {
        let expires = ''

        if (days) {
            const date = new Date()
            date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000)
            expires = '; expires=' + date.toUTCString()
        }

        document.cookie = name + '=' + (value || '') + expires + '; path=/'
    }

    const getCookie = (name) => {
        const nameEQ = name + '='
        const ca = document.cookie.split(';')
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i]
            while (c.charAt(0) == ' ') c = c.substring(1, c.length)
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length)
        }
        return null
    }

    const isMobile = {
        Android: function () {
            return navigator.userAgent.match(/Android/i)
        },
        BlackBerry: function () {
            return navigator.userAgent.match(/BlackBerry/i)
        },
        iOS: function () {
            return navigator.userAgent.match(/iPhone|iPad|iPod/i)
        },
        Opera: function () {
            return navigator.userAgent.match(/Opera Mini/i)
        },
        Windows: function () {
            return navigator.userAgent.match(/IEMobile/i)
        },
        any: function () {
            return (
                isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows()
            )
        },
    }

    /* Parallax
    -------------------------------------------------------------------------------------*/
    const parallax = function () {
        if ($().parallax && isMobile.any() == null) {
            $('.parallax').parallax('50%', 0.2)
        }
    }
    /* Content box
    -------------------------------------------------------------------------------------*/
    const flatContentBox = function () {
        $(window).on('load resize', function () {
            let mode = 'desktop'

            if (matchMedia('only screen and (max-width: 1199px)').matches) {
                mode = 'mobile'
            }

            $('.themesflat-content-box').each(function () {
                const margin = $(this).data('margin')
                if (margin) {
                    if (mode === 'desktop') {
                        $(this).attr('style', 'margin:' + $(this).data('margin'))
                    } else if (mode === 'mobile') {
                        $(this).attr('style', 'margin:' + $(this).data('mobilemargin'))
                    }
                }
            })
        })
    }
    /* Counter
    -------------------------------------------------------------------------------------*/
    const flatCounter = function () {
        const $counter = $('.tf-counter')

        if ($counter.length > 0 && $(document.body).hasClass('counter-scroll')) {
            let a = 0
            $(window).scroll(function () {
                const oTop = $counter.offset().top - window.innerHeight
                if (a === 0 && $(window).scrollTop() > oTop) {
                    if ($().countTo) {
                        $('.tf-counter')
                            .find('.number')
                            .each(function () {
                                const to = $(this).data('to'),
                                    speed = $(this).data('speed'),
                                    dec = $(this).data('dec')
                                $(this).countTo({
                                    to: to,
                                    speed: speed,
                                    decimals: dec,
                                })
                            })
                    }
                    a = 1
                }
            })
        }
    }

    new WOW().init()

    /* Sidebar Toggle
    -------------------------------------------------------------------------------------*/
    const sidebarToggle = function () {
        const args = {duration: 500}

        $('.btn-show-advanced').click(function () {
            $(this).parent('.inner-filter').find('.wd-amenities').slideDown(args)
            $('.inner-filter').addClass('active')
        })
        $('.btn-hide-advanced').click(function () {
            $(this).parent('.inner-filter').find('.wd-amenities').slideUp(args)
            $('.inner-filter').removeClass('active')
        })

        $('.btn-show-advanced-mb').click(function () {
            $(this).parent('.inner-filter').find('.wd-show-filter-mb').slideToggle(args)
        })
    }
    /* Lightbox
    -------------------------------------------------------------------------------------*/
    const popUpLightBox = function () {
        if ($('.lightbox-image').length) {
            $('.lightbox-image').fancybox({
                openEffect: 'fade',
                closeEffect: 'fade',
                helpers: {
                    media: {},
                },
            })
        }
    }
    /* Preloader
    -------------------------------------------------------------------------------------*/
    const preloader = function () {
        setTimeout(function () {
            $('.preload').fadeOut('slow', function () {
                $(this).remove()
            })
        }, 200)
    }

    /* Show Pass
    -------------------------------------------------------------------------------------*/
    const showPass = function () {
        $('.show-pass').on('click', function () {
            $(this).toggleClass('active')
            if ($('.password-field').attr('type') == 'password') {
                $('.password-field').attr('type', 'text')
            } else if ($('.password-field').attr('type') == 'text') {
                $('.password-field').attr('type', 'password')
            }
        })

        $('.show-pass2').on('click', function () {
            $(this).toggleClass('active')
            if ($('.password-field2').attr('type') == 'password') {
                $('.password-field2').attr('type', 'text')
            } else if ($('.password-field2').attr('type') == 'text') {
                $('.password-field2').attr('type', 'password')
            }
        })
        $('.show-pass3').on('click', function () {
            $(this).toggleClass('active')
            if ($('.password-field3').attr('type') == 'password') {
                $('.password-field3').attr('type', 'text')
            } else if ($('.password-field3').attr('type') == 'text') {
                $('.password-field3').attr('type', 'password')
            }
        })
    }
    /* Button Quantity
    -------------------------------------------------------------------------------------*/
    const btnQuantity = function () {
        $('.minus-btn').on('click', function (e) {
            e.preventDefault()
            const $this = $(this)
            const $input = $this.closest('div').find('input')
            let value = parseInt($input.val())

            if (value > 0) {
                value = value - 1
            }

            $input.val(value)
        })

        $('.plus-btn').on('click', function (e) {
            e.preventDefault()
            const $this = $(this)
            const $input = $this.closest('div').find('input')
            let value = parseInt($input.val())

            if (value > -1) {
                value = value + 1
            }

            $input.val(value)
        })
    }

    /* Input file
    -------------------------------------------------------------------------------------*/
    const flcustominput = function () {
        $('input[type=file]').change(function (e) {
            $(this).parents('.uploadfile').find('.file-name').text(e.target.files[0].name)
        })
    }

    /* Delete image
    -------------------------------------------------------------------------------------*/
    const delete_img = function () {
        $('.remove-file').on('click', function (e) {
            e.preventDefault()
            const $this = $(this)
            $this.closest('.file-delete').remove()
        })
    }
    /* Handle Search Form
    -------------------------------------------------------------------------------------*/
    const clickSearchForm = function () {
        const widgetSearchForm = $('.wd-search-form')
        if (widgetSearchForm.length) {
            $('.pull-right').on('click', function () {
                widgetSearchForm.toggleClass('show')
            })
            $(document).on('click', '.pull-right, .offcanvas-backdrop', function (a) {
                a.preventDefault()
                if ($(a.target).closest('.pull-right, .wd-search-form').length === 0) {
                    widgetSearchForm.removeClass('show')
                }
            })
        }
    }
    /* Datepicker
    -------------------------------------------------------------------------------------*/
    const datePicker = function () {
        if ($('#datepicker1').length > 0) {
            $('#datepicker1').datepicker({
                firstDay: 1,
                dateFormat: 'dd/mm/yy',
            })
        }
        if ($('#datepicker2').length > 0) {
            $('#datepicker2').datepicker({
                firstDay: 1,
                dateFormat: 'dd/mm/yy',
            })
        }
        if ($('#datepicker3').length > 0) {
            $('#datepicker3').datepicker({
                firstDay: 1,
                dateFormat: 'dd/mm/yy',
            })
        }
        if ($('#datepicker4').length > 0) {
            $('#datepicker4').datepicker({
                firstDay: 1,
                dateFormat: 'dd/mm/yy',
            })
        }
    }

    /* One Page
    -------------------------------------------------------------------------------------*/
    const onepageSingle = function () {
        if ($('.cate-single-tab').length) {
            const top_offset = $('.main-header').height() - 10
            $('.cate-single-tab').onePageNav({
                currentClass: 'active',
                scrollOffset: top_offset,
            })
        }
    }

    /* Handle dashboard
    -------------------------------------------------------------------------------------*/
    const showHideDashboard = function () {
        $('.button-show-hide').on('click', function () {
            $('.layout-wrap').toggleClass('full-width')
        })
        $('.mobile-nav-toggler,.overlay-dashboard').on('click', function () {
            $('.layout-wrap').removeClass('full-width')
        })
    }

    /* Go Top
    -------------------------------------------------------------------------------------*/
    const goTop = function () {
        if ($('div').hasClass('progress-wrap')) {
            const progressPath = document.querySelector('.progress-wrap path')
            const pathLength = progressPath.getTotalLength()
            progressPath.style.transition = progressPath.style.WebkitTransition = 'none'
            progressPath.style.strokeDasharray = pathLength + ' ' + pathLength
            progressPath.style.strokeDashoffset = pathLength
            progressPath.getBoundingClientRect()
            progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear'
            const updateprogress = function () {
                const scroll = $(window).scrollTop()
                const height = $(document).height() - $(window).height()
                const progress = pathLength - (scroll * pathLength) / height
                progressPath.style.strokeDashoffset = progress
            }
            updateprogress()
            $(window).scroll(updateprogress)
            const offset = 200
            const duration = 550
            jQuery(window).on('scroll', function () {
                if (jQuery(this).scrollTop() > offset) {
                    jQuery('.progress-wrap').addClass('active-progress')
                } else {
                    jQuery('.progress-wrap').removeClass('active-progress')
                }
            })
            jQuery('.progress-wrap').on('click', function (event) {
                event.preventDefault()
                jQuery('html, body').animate({scrollTop: 0}, duration)
                return false
            })
        }
    }

    /* Cursor
    -------------------------------------------------------------------------*/
    const cursor = function () {
        const myCursor = jQuery('.tf-mouse')
        if (myCursor.length) {
            if ($('body')) {
                const e = document.querySelector('.tf-mouse-inner'),
                    t = document.querySelector('.tf-mouse-outer')
                let n,
                    i = 0,
                    o = !1

                ;(window.onmousemove = function (s) {
                    o || (t.style.transform = 'translate(' + s.clientX + 'px, ' + s.clientY + 'px)'),
                        (e.style.transform = 'translate(' + s.clientX + 'px, ' + s.clientY + 'px)'),
                        (n = s.clientY),
                        (i = s.clientX)
                }),
                    (e.style.visibility = 'visible'),
                    (t.style.visibility = 'visible')
            }
        }
    }

    const themesflatTheme = {
        // Main init function
        init: function () {
            this.config()
            this.events()
        },

        // Define vars for caching
        config: function () {
            this.config = {
                $window: $(window),
                $document: $(document),
            }
        },

        // Events
        events: function () {
            const self = this

            // Run on document ready
            self.config.$document.on('ready', function () {
                // Retina Logos
                self.retinaLogo()
            })

            // Run on Window Load
            self.config.$window.on('load', function () {
            })
        },
    } // end themesflatTheme

    // Start things up
    themesflatTheme.init()

    /* RetinaLogo
    ------------------------------------------------------------------------------------- */
    const retinaLogos = function () {
        const retina = window.devicePixelRatio > 1 ? true : false
        if (retina) {
            $('#site-logo-inner').find('img').attr({
                src: 'assets/images/logo/logo@2x.png',
                width: '197',
                height: '48',
            })

            $('#logo-footer.style').find('img').attr({
                src: 'assets/images/logo/logo-footer@2x.png',
                width: '197',
                height: '48',
            })
            $('#logo-footer.style2').find('img').attr({
                src: 'assets/images/logo/logo@2x.png',
                width: '197',
                height: '48',
            })
        }
    }

    /* Header Fixed
    ------------------------------------------------------------------------------------- */
    const headerFixed = function () {
        if ($('header').hasClass('header-fixed')) {
            const nav = $('#header')
            if (nav.length) {
                const offsetTop = nav.offset().top,
                    headerHeight = nav.height(),
                    injectSpace = $('<div>', {
                        height: headerHeight,
                    })
                injectSpace.hide()

                $(window).on('load scroll', function () {
                    if ($(window).scrollTop() > 0) {
                        nav.addClass('is-fixed')
                        injectSpace.show()
                        $('#trans-logo').attr('src', 'images/logo/logo@2x.png')
                    } else {
                        nav.removeClass('is-fixed')
                        injectSpace.hide()
                        $('#trans-logo').attr('src', 'images/logo/logo-footer@2x.png')
                    }
                })
            }
        }
    }

    $('#showlogo').prepend('<a href="index.html"><img id="theImg" src="assets/images/logo/logo2.png" /></a>')

    // =========NICE SELECT=========

    if ($.isFunction($.fn.niceSelect)) {
        $('.select_js').niceSelect()
    }

    new WOW().init()

    //Submenu Dropdown Toggle
    if ($('.main-header li.dropdown2 ul').length) {
        $('.main-header li.dropdown2').append('<div class="dropdown2-btn"></div>')

        //Dropdown Button
        $('.main-header li.dropdown2 .dropdown2-btn').on('click', function () {
            $(this).prev('ul').slideToggle(500)
        })

        //Disable dropdown parent link
        $('.navigation li.dropdown2 > a').on('click', function (e) {
            e.preventDefault()
        })

        //Disable dropdown parent link
        $('.main-header .navigation li.dropdown2 > a,.hidden-bar .side-menu li.dropdown2 > a').on(
            'click',
            function (e) {
                e.preventDefault()
            }
        )

        $('.price-block .features .arrow').on('click', function (e) {
            $(e.target.offsetParent.offsetParent.offsetParent).toggleClass('active-show-hidden')
        })
    }

    // Mobile Nav Hide Show
    if ($('.mobile-menu').length) {
        //$('.mobile-menu .menu-box').mCustomScrollbar();

        const mobileMenuContent = $('.main-header .nav-outer .main-menu').html()
        $('.mobile-menu .menu-box .menu-outer').append(mobileMenuContent)
        $('.sticky-header .main-menu').append(mobileMenuContent)

        //Hide / Show Submenu
        $('.mobile-menu .navigation > li.dropdown2 > .dropdown2-btn').on('click', function (e) {
            e.preventDefault()
            const target = $(this).parent('li').children('ul')
            const args = {duration: 300}
            if ($(target).is(':visible')) {
                $(this).parent('li').removeClass('open')
                $(target).slideUp(args)
                $(this).parents('.navigation').children('li.dropdown2').removeClass('open')
                $(this).parents('.navigation').children('li.dropdown2 > ul').slideUp(args)
                return false
            } else {
                $(this).parents('.navigation').children('li.dropdown2').removeClass('open')
                $(this).parents('.navigation').children('li.dropdown2').children('ul').slideUp(args)
                $(this).parent('li').toggleClass('open')
                $(this).parent('li').children('ul').slideToggle(args)
            }
        })

        //3rd Level Nav
        $('.mobile-menu .navigation > li.dropdown2 > ul  > li.dropdown2 > .dropdown2-btn').on('click', function (e) {
            e.preventDefault()
            const targetInner = $(this).parent('li').children('ul')

            if ($(targetInner).is(':visible')) {
                $(this).parent('li').removeClass('open')
                $(targetInner).slideUp(500)
                $(this).parents('.navigation > ul').find('li.dropdown2').removeClass('open')
                $(this).parents('.navigation > ul').find('li.dropdown > ul').slideUp(500)
                return false
            } else {
                $(this).parents('.navigation > ul').find('li.dropdown2').removeClass('open')
                $(this).parents('.navigation > ul').find('li.dropdown2 > ul').slideUp(500)
                $(this).parent('li').toggleClass('open')
                $(this).parent('li').children('ul').slideToggle(500)
            }
        })

        //Menu Toggle Btn
        $('.mobile-nav-toggler').on('click', function () {
            $('body').addClass('mobile-menu-visible')
        })

        //Menu Toggle Btn
        $('.mobile-menu .menu-backdrop, .close-btn').on('click', function () {
            $('body').removeClass('mobile-menu-visible')
            $('.mobile-menu .navigation > li').removeClass('open')
            $('.mobile-menu .navigation li ul').slideUp(0)
        })

        $(document).keydown(function (e) {
            if (e.keyCode === 27) {
                $('body').removeClass('mobile-menu-visible')
                $('.mobile-menu .navigation > li').removeClass('open')
                $('.mobile-menu .navigation li ul').slideUp(0)
            }
        })
    }

    /* alert box
    ------------------------------------------------------------------------------------- */
    const alertBox = function () {
        $(document).on('click', '.close', function (e) {
            $(this).closest('.flat-alert').remove()
            e.preventDefault()
        })
    }

    $(window).on('load resize', function () {
        retinaLogos()
    })

    $(document).on('submit', 'form.subscribe-form', (e) => {
        e.preventDefault()

        const $form = $(e.currentTarget)
        const $button = $form.find('button[type=submit]')

        $.ajax({
            type: 'POST',
            cache: false,
            url: $form.prop('action'),
            data: new FormData($form[0]),
            contentType: false,
            processData: false,
            beforeSend: () => $button.prop('disabled', true).addClass('btn-loading'),
            success: ({error, message}) => {
                if (error) {
                    Theme.showError(message)

                    return
                }

                $form.find('input[name="email"]').val('')

                Theme.showSuccess(message)

                document.dispatchEvent(new CustomEvent('newsletter.subscribed'))
            },
            error: (error) => Theme.handleError(error),
            complete: () => {
                if (typeof refreshRecaptcha !== 'undefined') {
                    refreshRecaptcha()
                }

                $button.prop('disabled', false).removeClass('btn-loading')
            },
        })
    })

    const animateHeading = () => {
        //set animation timing
        var animationDelay = 2500,
            //loading bar effect
            barAnimationDelay = 3800,
            barWaiting = barAnimationDelay - 3000, //3000 is the duration of the transition on the loading bar - set in the scss/css file
            //letters effect
            lettersDelay = 50,
            //type effect
            typeLettersDelay = 150,
            selectionDuration = 500,
            typeAnimationDelay = selectionDuration + 800,
            //clip effect
            revealDuration = 600,
            revealAnimationDelay = 1500

        initHeadline()

        function initHeadline() {
            //insert <i> element for each letter of a changing word
            singleLetters($('.animationtext.letters').find('.item-text'))
            //initialise headline animation
            animateHeadline($('.animationtext'))
        }

        function singleLetters($words) {
            $words.each(function () {
                var word = $(this),
                    letters = word.text().split(''),
                    selected = word.hasClass('is-visible')
                for (i in letters) {
                    if (word.parents('.rotate-2').length > 0) letters[i] = '<em>' + letters[i] + '</em>'
                    letters[i] = selected ? '<i class="in">' + letters[i] + '</i>' : '<i>' + letters[i] + '</i>'
                }
                var newLetters = letters.join('')
                word.html(newLetters).css('opacity', 1)
            })
        }

        function animateHeadline($headlines) {
            var duration = animationDelay
            $headlines.each(function () {
                var headline = $(this)

                if (headline.hasClass('loading-bar')) {
                    duration = barAnimationDelay
                    setTimeout(function () {
                        headline.find('.cd-words-wrapper').addClass('is-loading')
                    }, barWaiting)
                } else if (headline.hasClass('clip')) {
                    var spanWrapper = headline.find('.cd-words-wrapper'),
                        newWidth = spanWrapper.width() + 10
                    spanWrapper.css('width', newWidth)
                } else if (!headline.hasClass('type')) {
                    //assign to .cd-words-wrapper the width of its longest word
                    var words = headline.find('.cd-words-wrapper .item-text'),
                        width = 0
                    words.each(function () {
                        var wordWidth = $(this).width()
                        if (wordWidth > width) width = wordWidth
                    })
                    headline.find('.cd-words-wrapper').css('width', width)
                }

                //trigger animation
                setTimeout(function () {
                    hideWord(headline.find('.is-visible').eq(0))
                }, duration)
            })
        }

        function hideWord($word) {
            var nextWord = takeNext($word)

            if ($word.parents('.animationtext').hasClass('type')) {
                var parentSpan = $word.parent('.cd-words-wrapper')
                parentSpan.addClass('selected').removeClass('waiting')
                setTimeout(function () {
                    parentSpan.removeClass('selected')
                    $word
                        .removeClass('is-visible')
                        .addClass('is-hidden')
                        .children('i')
                        .removeClass('in')
                        .addClass('out')
                }, selectionDuration)
                setTimeout(function () {
                    showWord(nextWord, typeLettersDelay)
                }, typeAnimationDelay)
            } else if ($word.parents('.animationtext').hasClass('letters')) {
                var bool = $word.children('i').length >= nextWord.children('i').length
                hideLetter($word.find('i').eq(0), $word, bool, lettersDelay)
                showLetter(nextWord.find('i').eq(0), nextWord, bool, lettersDelay)
            } else if ($word.parents('.animationtext').hasClass('clip')) {
                $word.parents('.cd-words-wrapper').animate({width: '2px'}, revealDuration, function () {
                    switchWord($word, nextWord)
                    showWord(nextWord)
                })
            } else if ($word.parents('.animationtext').hasClass('loading-bar')) {
                $word.parents('.cd-words-wrapper').removeClass('is-loading')
                switchWord($word, nextWord)
                setTimeout(function () {
                    hideWord(nextWord)
                }, barAnimationDelay)
                setTimeout(function () {
                    $word.parents('.cd-words-wrapper').addClass('is-loading')
                }, barWaiting)
            } else {
                switchWord($word, nextWord)
                setTimeout(function () {
                    hideWord(nextWord)
                }, animationDelay)
            }
        }

        function showWord($word, $duration) {
            if ($word.parents('.animationtext').hasClass('type')) {
                showLetter($word.find('i').eq(0), $word, false, $duration)
                $word.addClass('is-visible').removeClass('is-hidden')
            } else if ($word.parents('.animationtext').hasClass('clip')) {
                $word.parents('.cd-words-wrapper').animate({width: $word.width() + 10}, revealDuration, function () {
                    setTimeout(function () {
                        hideWord($word)
                    }, revealAnimationDelay)
                })
            }
        }

        function hideLetter($letter, $word, $bool, $duration) {
            $letter.removeClass('in').addClass('out')

            if (!$letter.is(':last-child')) {
                setTimeout(function () {
                    hideLetter($letter.next(), $word, $bool, $duration)
                }, $duration)
            } else if ($bool) {
                setTimeout(function () {
                    hideWord(takeNext($word))
                }, animationDelay)
            }

            if ($letter.is(':last-child') && $('html').hasClass('no-csstransitions')) {
                var nextWord = takeNext($word)
                switchWord($word, nextWord)
            }
        }

        function showLetter($letter, $word, $bool, $duration) {
            $letter.addClass('in').removeClass('out')

            if (!$letter.is(':last-child')) {
                setTimeout(function () {
                    showLetter($letter.next(), $word, $bool, $duration)
                }, $duration)
            } else {
                if ($word.parents('.animationtext').hasClass('type')) {
                    setTimeout(function () {
                        $word.parents('.cd-words-wrapper').addClass('waiting')
                    }, 200)
                }
                if (!$bool) {
                    setTimeout(function () {
                        hideWord($word)
                    }, animationDelay)
                }
            }
        }

        function takeNext($word) {
            return !$word.is(':last-child') ? $word.next() : $word.parent().children().eq(0)
        }

        function takePrev($word) {
            return !$word.is(':first-child') ? $word.prev() : $word.parent().children().last()
        }

        function switchWord($oldWord, $newWord) {
            $oldWord.removeClass('is-visible').addClass('is-hidden')
            $newWord.removeClass('is-hidden').addClass('is-visible')
        }
    }

    const rangeSlider = () => {
        if (typeof wNumb === 'undefined' || typeof noUiSlider === 'undefined') {
            return
        }

        const priceSlider = () => {
            $('.noUi-handle').on('click', function () {
                $(this).width(50)
            })

            $('[data-bb-toggle="range"]').each((index, el) => {
                const $element = $(el)
                const rangeSlider = $element.find('[data-bb-toggle="range-slider"]').get(0)
                const $minInput = $element.find('.slider-labels input[data-bb-toggle="min-input"]')
                const $maxInput = $element.find('.slider-labels input[data-bb-toggle="max-input"]')

                const currencySymbol = $(rangeSlider).data('currency-symbol') || '$'

                let moneyFormatOptions = {
                    decimals: 0,
                    thousand: ',',
                }

                const currencyWithSpace = $(rangeSlider).data('currency-with-space')

                if ($(rangeSlider).data('currency-prefix-symbol')) {
                    moneyFormatOptions.prefix = currencySymbol + (currencyWithSpace ? ' ' : '')
                } else {
                    moneyFormatOptions.postfix = (currencyWithSpace ? ' ' : '') + currencySymbol
                }

                const moneyFormat = wNumb(moneyFormatOptions)

                noUiSlider.create(rangeSlider, {
                    start: [parseInt($minInput.val() || $element.data('min')) || 0, parseInt($maxInput.val() || $element.data('max')) || 0],
                    step: 1,
                    range: {
                        min: [parseInt($element.data('min'))],
                        max: [parseInt($element.data('max'))],
                    },
                    format: moneyFormat,
                    connect: true,
                })

                rangeSlider.noUiSlider.on('update', function (values, handle) {
                    $element.find('[data-bb-toggle="range-from-value"]').html(values[0])
                    $element.find('[data-bb-toggle="range-to-value"]').html(values[1])
                })

                rangeSlider.noUiSlider.on('change', function (values) {
                    $minInput.val(moneyFormat.from(values[0])).trigger('change')
                    $maxInput.val(moneyFormat.from(values[1])).trigger('change')
                })
            })
        }

        const squareSlider = () => {
            $('.noUi-handle2').on('click', function () {
                $(this).width(50)
            })

            const rangeSlider = $('#slider-range2').get(0)

            if (!rangeSlider) {
                return
            }

            const unit = $(rangeSlider).data('unit')

            const moneyFormat = wNumb({
                decimals: 0,
                thousand: ',',
                postfix: unit ? ` ${$(rangeSlider).data('unit')}` : '',
            })

            const $minSquare = $('.slider-labels input[name="min_square"]')
            const $maxSquare = $('.slider-labels input[name="max_square"]')

            noUiSlider.create(rangeSlider, {
                start: [parseInt($minSquare.val() || $(rangeSlider).data('min')), parseInt($maxSquare.val() || $(rangeSlider).data('max'))],
                step: 1,
                range: {
                    min: [$(rangeSlider).data('min')],
                    max: [$(rangeSlider).data('max')],
                },
                format: moneyFormat,
                connect: true,
            })

            rangeSlider.noUiSlider.on('update', function (values, handle) {
                document.getElementById('slider-range-value01').innerHTML = values[0]
                document.getElementById('slider-range-value02').innerHTML = values[1]
            })

            rangeSlider.noUiSlider.on('change', function (values) {
                $('.slider-labels input[name="min_square"]').val(moneyFormat.from(values[0])).trigger('change')
                $('.slider-labels input[name="max_square"]').val(moneyFormat.from(values[1])).trigger('change')
            })
        }

        const flatSlider = () => {
            const rangeSlider = $('#slider-flat').get(0)

            if (!rangeSlider) {
                return
            }

            const unit = $(rangeSlider).data('unit')

            const moneyFormat = wNumb({
                decimals: 0,
                thousand: ',',
                postfix: unit ? ` ${$(rangeSlider).data('unit')}` : '',
            })

            const $minFlat = $('.slider-labels input[name="min_flat"]')
            const $maxFlat = $('.slider-labels input[name="max_flat"]')

            noUiSlider.create(rangeSlider, {
                start: [parseInt($minFlat.val() || $(rangeSlider).data('min')), parseInt($maxFlat.val() || $(rangeSlider).data('max'))],
                step: 1,
                range: {
                    min: [$(rangeSlider).data('min')],
                    max: [$(rangeSlider).data('max')],
                },
                format: moneyFormat,
                connect: true,
            })

            rangeSlider.noUiSlider.on('update', function (values, handle) {
                document.getElementById('slider-flat-value01').innerHTML = values[0]
                document.getElementById('slider-flat-value02').innerHTML = values[1]
            })

            rangeSlider.noUiSlider.on('change', function (values) {
                $('.slider-labels input[name="min_flat"]').val(moneyFormat.from(values[0])).trigger('change')
                $('.slider-labels input[name="max_flat"]').val(moneyFormat.from(values[1])).trigger('change')
            })
        }

        priceSlider()
        squareSlider()
        flatSlider()
    }

    rangeSlider()
    headerFixed()
    alertBox()
    flatContentBox()
    popUpLightBox()
    parallax()
    flatCounter()
    flcustominput()
    btnQuantity()
    delete_img()
    clickSearchForm()
    sidebarToggle()
    onepageSingle()
    showHideDashboard()
    goTop()
    showPass()
    datePicker()
    preloader()
    // cursor();
    animateHeading()

    const Spanizer = (function () {
        const settings = {
            letters: $('.js-letters'),
        }
        return {
            init: function () {
                this.bind()
            },
            bind: function () {
                Spanizer.doSpanize()
            },
            doSpanize: function () {
                settings.letters.html(function (i, el) {
                    const spanize = $.trim(el).split('')

                    return `<span>${spanize.join('</span><span>')}</span>`
                })
            },
        }
    })()
    // Let's GO!

    if (matchMedia('only screen and (min-width: 991px)').matches) {
        Spanizer.init()
    }

    if ($('.thumbs-swiper-column').length > 0) {
        const swiperthumbs = new Swiper('.thumbs-swiper-column1', {
            rtl: Theme.isRtl(),
            spaceBetween: 0,
            slidesPerView: 4,
            freeMode: true,
            direction: 'vertical',
            watchSlidesProgress: true,
        })

        const swiper2 = new Swiper('.thumbs-swiper-column', {
            rtl: Theme.isRtl(),
            spaceBetween: 0,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            speed: 500,
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
            thumbs: {
                swiper: swiperthumbs,
            },
        })
    }

    if ($('.slider-sw-home2').length > 0) {
        const swiper2 = new Swiper('.slider-sw-home2', {
            rtl: Theme.isRtl(),
            spaceBetween: 0,
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
            },
            speed: 2000,
            effect: 'fade',
            fadeEffect: {
                crossFade: true,
            },
        })
    }

    if ($('.tf-sw-auto').length > 0) {
        const loop = $('.tf-sw-auto').data('loop')

        const swiper = new Swiper('.tf-sw-auto', {
            rtl: Theme.isRtl(),
            autoplay: {
                delay: 1500,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },
            speed: 2000,
            slidesPerView: 'auto',
            spaceBetween: 0,
            loop: loop,
            navigation: {
                clickable: true,
                nextEl: '.nav-prev-category',
                prevEl: '.nav-next-category',
            },
        })
    }

    const pagithumbs = new Swiper('.thumbs-sw-pagi', {
        rtl: Theme.isRtl(),
        spaceBetween: 14,
        slidesPerView: 'auto',
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            375: {
                slidesPerView: 3,
                spaceBetween: 14,
            },
            500: {
                slidesPerView: 'auto',
            },
        },
    })

    const swiperSingle = new Swiper('.sw-single', {
        rtl: Theme.isRtl(),
        spaceBetween: 16,
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        speed: 500,
        effect: 'fade',
        fadeEffect: {
            crossFade: true,
        },
        thumbs: {
            swiper: pagithumbs,
        },
        navigation: {
            clickable: true,
            nextEl: '.nav-prev-single',
            prevEl: '.nav-next-single',
        },
    })

    if ($('.tf-latest-property').length > 0) {
        const previewLg = $('.tf-latest-property').data('preview-lg')
        const previewMd = $('.tf-latest-property').data('preview-md')
        const previewSm = $('.tf-latest-property').data('preview-sm')
        const spacing = $('.tf-latest-property').data('space')
        const centered = $('.tf-latest-property').data('centered')
        const loop = $('.tf-latest-property').data('loop')
        const swiper = new Swiper('.tf-latest-property', {
            rtl: Theme.isRtl(),
            autoplay: {
                delay: 2000,
                disableOnInteraction: false,
                reverseDirection: false,
            },

            speed: 3000,
            slidesPerView: 1,
            loop: loop,
            spaceBetween: spacing,
            centeredSlides: centered,
            breakpoints: {
                600: {
                    slidesPerView: previewSm,
                    spaceBetween: 20,
                    centeredSlides: false,
                },
                991: {
                    slidesPerView: previewMd,
                    spaceBetween: 20,
                    centeredSlides: false,
                },

                1550: {
                    slidesPerView: previewLg,
                    spaceBetween: spacing,
                },
            },
        })
    }

    const initImageSlider = () => {
        if ($('.tf-sw-partner').length > 0) {
            const $element = $('.tf-sw-partner')
            const previewLg = $element.data('preview-lg')
            const previewMd = $element.data('preview-md')
            const previewSm = $element.data('preview-sm')
            const spacing = $element.data('space')
            const autoplay = $element.data('autoplay')
            const autoplaySpeed = $element.data('autoplay-speed')
            const loop = $element.data('loop')
            const swiper = new Swiper('.tf-sw-partner', {
                rtl: Theme.isRtl(),
                autoplay: autoplay ? {
                    delay: autoplaySpeed,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                } : false,
                slidesPerView: 2,
                loop: loop,
                spaceBetween: 30,
                speed: 3000,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    450: {
                        slidesPerView: previewSm,
                        spaceBetween: 30,
                    },
                    768: {
                        slidesPerView: previewMd,
                        spaceBetween: 30,
                    },

                    992: {
                        slidesPerView: previewLg,
                        spaceBetween: spacing,
                    },
                },
            })
        }

        $('.tf-sw-partner').hover(
            function () {
                this.swiper.autoplay.stop()
            },
            function () {
                this.swiper.autoplay.start()
            }
        )
    }

    const initPropertyCategories = () => {
        if ($('.tf-sw-categories').length > 0) {
            const $element = $('.tf-sw-categories')
            const previewLg = $element.data('preview-lg')
            const previewMd = $element.data('preview-md')
            const previewSm = $element.data('preview-sm')
            const spacing = $element.data('space')
            const autoplay = $element.data('autoplay')
            const autoplaySpeed = $element.data('autoplay-speed')
            const loop = $element.data('loop')
            const swiper = new Swiper('.tf-sw-categories', {
                rtl: Theme.isRtl(),
                slidesPerView: 2,
                spaceBetween: 30,
                loop: loop,
                autoplay: autoplay ? {
                    delay: autoplaySpeed,
                } : false,
                navigation: {
                    clickable: true,
                    nextEl: '.nav-prev-category',
                    prevEl: '.nav-next-category',
                },
                pagination: {
                    el: '.sw-pagination-category',
                    clickable: true,
                },
                breakpoints: {
                    600: {
                        slidesPerView: previewSm,
                        spaceBetween: 30,
                    },
                    800: {
                        slidesPerView: previewMd,
                        spaceBetween: 30,
                    },

                    1300: {
                        slidesPerView: previewLg,
                        spaceBetween: spacing,
                    },
                },
            })
        }
    }

    const initTestimonials = () => {
        if ($('.tf-sw-testimonial').length > 0) {
            const $element = $('.tf-sw-testimonial')
            const previewLg = $element.data('preview-lg')
            const previewMd = $element.data('preview-md')
            const previewSm = $element.data('preview-sm')
            const spacing = $element.data('space')
            const autoplay = $element.data('autoplay')
            const autoplaySpeed = $element.data('autoplay-speed')
            const loop = $element.data('loop')
            const swTestimonial = new Swiper('.tf-sw-testimonial', {
                rtl: Theme.isRtl(),
                loop: loop,
                autoplay: autoplay ? {
                    delay: autoplaySpeed,
                } : false,
                slidesPerView: 1,
                spaceBetween: spacing,
                navigation: {
                    clickable: true,
                    nextEl: '.nav-prev-testimonial',
                    prevEl: '.nav-next-testimonial',
                },
                pagination: {
                    el: '.sw-pagination-testimonial',
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: previewSm,
                        spaceBetween: 20,
                    },
                    991: {
                        slidesPerView: previewMd,
                        spaceBetween: 20,
                    },

                    1550: {
                        slidesPerView: previewLg,
                        spaceBetween: spacing,
                    },
                },
            })
        }
    }

    const initLocation = () => {
        if ($('.tf-sw-location').length > 0) {
            const $element = $('.tf-sw-location')
            const previewLg = $element.data('preview-lg')
            const previewMd = $element.data('preview-md')
            const previewSm = $element.data('preview-sm')
            const spacing = $element.data('space')
            const centered = $element.data('centered')
            const autoplay = $element.data('autoplay')
            const autoplaySpeed = $element.data('autoplay-speed')
            const loop = $element.data('loop')

            const swiper = new Swiper('.tf-sw-location', {
                rtl: Theme.isRtl(),
                autoplay: autoplay ? {
                    delay: autoplaySpeed,
                    disableOnInteraction: false,
                } : false,
                speed: 750,
                navigation: {
                    clickable: true,
                    nextEl: '.nav-prev-location',
                    prevEl: '.nav-next-location',
                },
                pagination: {
                    el: '.swiper-pagination1',
                    clickable: true,
                },
                slidesPerView: 1,
                loop: loop,
                spaceBetween: spacing,
                centeredSlides: centered,
                breakpoints: {
                    600: {
                        slidesPerView: previewSm,
                        spaceBetween: 20,
                        centeredSlides: false,
                    },
                    991: {
                        slidesPerView: previewMd,
                        spaceBetween: 20,
                        centeredSlides: false,
                    },

                    1520: {
                        slidesPerView: previewLg,
                        spaceBetween: spacing,
                    },
                },
            })
        }
    }

    const initPropertiesTab = () => {
        $(document)
            .off('click', '[data-bb-toggle="properties-tab"] [data-bs-toggle="tab"]')
            .on('click', '[data-bb-toggle="properties-tab"] [data-bs-toggle="tab"]', (e) => {
                const currentTarget = $(e.currentTarget)
                const tab = currentTarget.closest('[data-bb-toggle="properties-tab"]')
                const data = tab.data('attributes')

                data['category_id'] = currentTarget.data('bb-value')

                const parentTab = currentTarget.closest('.flat-tab-recommended')

                $.ajax({
                    url: tab.data('url'),
                    method: 'GET',
                    dataType: 'json',
                    data: data,
                    beforeSend: () => {
                        parentTab.append('<div class="loading-spinner"></div>')
                    },
                    success: ({data}) => {
                        parentTab.find('[data-bb-toggle="properties-tab-slot"]').html(data)

                        if (typeof Theme.lazyLoadInstance !== 'undefined') {
                            Theme.lazyLoadInstance.update()
                        }

                        initWishlist()
                    },
                    error: (error) => Theme.handleError(error),
                    complete: () => parentTab.find('.loading-spinner').remove(),
                })
            })
    }

    const initServices = () => {
        if ($('.tf-sw-benefit').length > 0) {
            new Swiper('.tf-sw-benefit', {
                rtl: Theme.isRtl(),
                slidesPerView: 1,
                spaceBetween: 30,
                navigation: {
                    clickable: true,
                    nextEl: '.nav-prev-benefit',
                    prevEl: '.nav-next-benefit',
                },
                pagination: {
                    el: '.sw-pagination-benefit',
                    clickable: true,
                },
            })
        }
    }

    function cleanFormData(formDataInput) {
        const formData = formDataInput.filter((item) => item.value !== '' && (item.name !== 'per_page' || (item.name === 'per_page' && parseInt(item.value) !== 12)))

        let queryString = formData
            .filter((item) => item.name !== '_token')
            .map((item) => `${encodeURIComponent(item.name)}=${encodeURIComponent(item.value)}`)

        queryString = queryString.length > 0 ? `?${queryString.join('&')}` : ''

        return {
            formData: formData,
            queryString: queryString,
        }
    }

    const initProperties = () => {
        if ($('.tf-sw-property').length > 0) {
            new Swiper('.tf-sw-property', {
                rtl: Theme.isRtl(),
                slidesPerView: 1,
                spaceBetween: 30,
                navigation: {
                    clickable: true,
                    nextEl: '.nav-prev-property',
                    prevEl: '.nav-next-property',
                },
                pagination: {
                    el: '.sw-pagination-property',
                    clickable: true,
                },
            })
        }
    }

    initImageSlider()
    initImageSlider()
    initLocation()
    initPropertiesTab()
    initPropertyCategories()
    initProperties()
    initServices()
    initTestimonials()

    $('[data-bb-toggle="detail-map"]').each((index, element) => {
        const $element = $(element)

        const map = L.map($element.prop('id'), {
            attributionControl: false,
        }).setView($element.data('center'), 14)

        L.tileLayer($element.data('tile-layer'), {
            maxZoom: $element.data('max-zoom') || 22,
        }).addTo(map)

        L.marker($element.data('center'), {
            icon: L.divIcon({
                iconSize: L.point(50, 50),
                className: 'map-marker-home',
            }),
        })
            .addTo(map)
            .bindPopup($('#map-popup-content').html())
            .openPopup()

        if (typeof Theme.lazyLoadInstance !== 'undefined') {
            Theme.lazyLoadInstance.update()
        }
    })

    const initMap = (formData) => {
        const $element = $('[data-bb-toggle="list-map"]')

        if ($element.length < 1) {
            return
        }

        if (window.activeMap) {
            window.activeMap.remove()
        }

        let center = $element.data('center')

        const centerFirst = $('.homeya-box[data-lat][data-lng]').filter(
            (index, item) => $(item).data('lat') && $(item).data('lng')
        )

        if (centerFirst && centerFirst.length) {
            center = [centerFirst.data('lat'), centerFirst.data('lng')]
        }

        const map = L.map($element.prop('id'), {
            attributionControl: false,
        }).setView(center, 14)

        L.tileLayer($element.data('tile-layer'), {
            maxZoom: $element.data('max-zoom') || 22,
        }).addTo(map)

        let totalPage = 0
        let currentPage = 1
        const markers = L.markerClusterGroup()

        const populate = () => {
            if (typeof formData === 'undefined') {
                const urlParams = new URLSearchParams(window.location.search)

                formData = {}

                if (urlParams.size > 0) {
                    for (const [key, value] of urlParams) {
                        formData[key] = value
                    }
                } else {
                    formData = {
                        page: 1,
                    }
                }
            } else if (Array.isArray(formData)) {
                formData = formData.reduce((acc, {name, value}) => {
                    acc[name] = value

                    return acc
                }, {})
            }

            formData.page = currentPage

            if (totalPage === 0 || currentPage <= totalPage) {
                $.ajax({
                    url: $element.data('url'),
                    type: 'GET',
                    data: formData,
                    success: ({data, meta}) => {
                        if (data.length < 1) {
                            return
                        }

                        data.forEach((item) => {
                            if (!item.latitude || !item.longitude) {
                                return
                            }

                            const isProperty = typeof item.square !== 'undefined'

                            let content = isProperty
                                ? $('#property-map-content').html()
                                : $('#project-map-content').html()

                            content = content
                                .replace(new RegExp('__name__', 'gi'), item.name)
                                .replace(new RegExp('__location__', 'gi'), item.location)
                                .replace(new RegExp('__image__', 'gi'), item.image_thumb)
                                .replace(new RegExp('__price__', 'gi'), item.formatted_price)
                                .replace(new RegExp('__url__', 'gi'), item.url)
                                .replace(new RegExp('__status__', 'gi'), item.status_html)

                            if (isProperty) {
                                content = content
                                    .replace(new RegExp('__bedroom__', 'gi'), item.number_bedroom)
                                    .replace(new RegExp('__bathroom__', 'gi'), item.number_bathroom)
                                    .replace(new RegExp('__square__', 'gi'), item.square_text)
                            }

                            const marker = L.marker(L.latLng(item.latitude, item.longitude), {
                                icon: L.divIcon({
                                    iconSize: L.point(50, 20),
                                    className: 'boxmarker',
                                    html: item.map_icon,
                                }),
                            })
                                .bindPopup(content, {maxWidth: '100%'})
                                .addTo(map)

                            markers.addLayer(marker)

                            map.flyToBounds(markers.getBounds())
                        })

                        if (totalPage === 0) {
                            totalPage = meta.last_page
                        }
                        currentPage++
                        populate()
                    },
                })
            }
        }

        populate()

        map.addLayer(markers)

        window.activeMap = map
    }

    initMap()

    let projectSearchTimeout = null

    const initWishlistCount = () => {
        const wishlist = decodeURIComponent(getCookie('wishlist') || '')
        const projectWishlist = decodeURIComponent(getCookie('project_wishlist') || '')

        const wishlistArray = wishlist ? wishlist.split(',') : []
        const projectWishlistArray = projectWishlist ? projectWishlist.split(',') : []

        $('[data-bb-toggle="wishlist-count"]').text(wishlistArray.length + projectWishlistArray.length)
    }

    const initWishlist = () => {
        const wishlist = decodeURIComponent(getCookie('wishlist') || '')
        const projectWishlist = decodeURIComponent(getCookie('project_wishlist') || '')

        const wishlistArray = wishlist ? wishlist.split(',') : []
        const projectWishlistArray = projectWishlist ? projectWishlist.split(',') : []

        wishlistArray.forEach((id) => {
            $(`[data-bb-toggle="add-to-wishlist"][data-type="property"][data-id="${id}"]`).addClass('active').html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M6.979 3.074a6 6 0 0 1 4.988 1.425l.037 .033l.034 -.03a6 6 0 0 1 4.733 -1.44l.246 .036a6 6 0 0 1 3.364 10.008l-.18 .185l-.048 .041l-7.45 7.379a1 1 0 0 1 -1.313 .082l-.094 -.082l-7.493 -7.422a6 6 0 0 1 3.176 -10.215z" />
                </svg>
            `)
        })

        projectWishlistArray.forEach((id) => {
            $(`[data-bb-toggle="add-to-wishlist"][data-type="project"][data-id="${id}"]`).addClass('active').html(`
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M6.979 3.074a6 6 0 0 1 4.988 1.425l.037 .033l.034 -.03a6 6 0 0 1 4.733 -1.44l.246 .036a6 6 0 0 1 3.364 10.008l-.18 .185l-.048 .041l-7.45 7.379a1 1 0 0 1 -1.313 .082l-.094 -.082l-7.493 -7.422a6 6 0 0 1 3.176 -10.215z" />
                </svg>
            `)
        })

        initWishlistCount()
    }

    initWishlist()

    $(document)
        .on('submit', '.contact-form', function (event) {
            event.preventDefault()
            event.stopPropagation()

            const $form = $(this)
            const $button = $form.find('button[type=submit]')

            $.ajax({
                type: 'POST',
                cache: false,
                url: $form.prop('action'),
                data: new FormData($form[0]),
                contentType: false,
                processData: false,
                beforeSend: () => $button.addClass('btn-loading'),
                success: ({error, message}) => {
                    if (!error) {
                        $form[0].reset()
                        Theme.showSuccess(message)
                    } else {
                        Theme.showError(message)
                    }
                },
                error: (error) => {
                    Theme.handleError(error)
                },
                complete: () => {
                    if (typeof refreshRecaptcha !== 'undefined') {
                        refreshRecaptcha()
                    }

                    $button.removeClass('btn-loading')
                },
            })
        })
        .on('change', '.filter-form select[name="sort_by"], .filter-form select[name="per_page"]', (e) => {
            $(e.currentTarget).closest('form').trigger('submit')
        })
        .on('click', '[data-bb-toggle="change-layout"]', (e) => {
            const $button = $(e.currentTarget)
            const $form = $button.closest('form')

            $form.find('input[name="layout"]').val($button.data('value'))
        })
        .on('click', '.filter-form .flat-pagination a', (e) => {
            e.preventDefault()

            const url = new URL(e.currentTarget.href)
            const $form = $(e.currentTarget).closest('form')

            $form.find('input[name="page"]').val(url.searchParams.get('page'))
            $form.trigger('submit')
        })
        .on('submit', '.filter-form', (e) => {
            e.preventDefault()

            $('.wd-search-form').removeClass('show')
            $('.search-box-offcanvas').removeClass('active')

            const $dataListing = $('[data-bb-toggle="data-listing"]')
            const $form = $(e.currentTarget)
            const cleanedFormData = cleanFormData($form.serializeArray())

            const nextHref = $form.prop('action') + cleanedFormData.queryString

            $.ajax({
                url: $form.data('url') || $form.prop('action'),
                type: 'POST',
                data: cleanedFormData.formData,
                beforeSend: () => {
                    $dataListing.append('<div class="loading-spinner"></div>')
                },
                success: function ({error, data, message}) {
                    if (error) {
                        Theme.showError(message)

                        return
                    }

                    $dataListing.html(data)

                    if (typeof Theme.lazyLoadInstance !== 'undefined') {
                        Theme.lazyLoadInstance.update()
                    }

                    initMap(cleanedFormData.formData)

                    if (nextHref !== window.location.href) {
                        window.history.pushState(cleanedFormData.formData, message, nextHref)

                        $('.reset-filter-btn').show()
                    }
                },
                complete: () => {
                    $dataListing.find('.loading-spinner').remove()

                    $('html, body').animate({
                        scrollTop: $dataListing.offset().top - 100,
                    })
                },
            })
        })
        .on('submit', '#hero-search-form', function (e) {
            e.preventDefault()

            const $form = $(e.currentTarget)

            const cleanedFormData = cleanFormData($form.serializeArray())

            window.location.href = $form.prop('action') + cleanedFormData.queryString
        })
        .on('keyup', '[data-bb-toggle="search-suggestion"] input[type="text"]', (e) => {
            clearTimeout(projectSearchTimeout)

            const $currentTarget = $(e.currentTarget)
            const $suggest = $currentTarget
                .closest('[data-bb-toggle="search-suggestion"]')
                .find('[data-bb-toggle="data-suggestion"]')

            const $form = $currentTarget.closest('form')

            const cleanedFormData = cleanFormData($form.serializeArray())

            cleanedFormData.formData.push({name: 'minimal', value: 0})

            projectSearchTimeout = setTimeout(() => {
                $.ajax({
                    url: $currentTarget.data('url') || $currentTarget.closest('form').prop('action'),
                    type: 'GET',
                    data: cleanedFormData.formData,
                    success: ({data}) => {
                        $suggest.html(data).slideDown()

                        if (typeof Theme.lazyLoadInstance !== 'undefined') {
                            Theme.lazyLoadInstance.update()
                        }
                    },
                })
            }, 500)
        })
        .on('click', '.search-suggestion-item:not([data-no-prevent])', (e) => {
            const $currentTarget = $(e.currentTarget)
            const $search = $currentTarget.closest('[data-bb-toggle="search-suggestion"]')
            const $hiddenInput = $search.find('input[type="hidden"]')

            $search.find('input[type="text"]').val($currentTarget.text())

            if ($hiddenInput.length > 0) {
                $hiddenInput.val($currentTarget.data('value')).trigger('change')
            }

            $search.find('[data-bb-toggle="data-suggestion"]').hide()
        })
        .on('keydown', '[data-bb-toggle="search-suggestion"] input[type="text"]', (e) => {
            $(e.currentTarget)
                .closest('[data-bb-toggle="search-suggestion"]')
                .find('[data-bb-toggle="data-suggestion"]')
                .slideUp()
        })
        .on('click', (e) => {
            if (!$(e.target).closest('[data-bb-toggle="data-suggestion"]').length) {
                $('[data-bb-toggle="data-suggestion"]').slideUp()
            }
        })
        .on('click', '[data-bb-toggle="change-search-type"]', (e) => {
            const currentTarget = $(e.currentTarget)
            const form = currentTarget.closest('.flat-tab').find('form')

            form.find('input[name="type"]').val(currentTarget.data('value')).trigger('change')
            form.prop('action', currentTarget.data('url'))

            form.find('input[name="k"]').attr('data-url', currentTarget.data('url'))

            if (currentTarget.data('value') === 'project') {
                $('.project-search-form').show()
                $('.property-search-form').hide()

                $('.project-search-form input').prop('disabled', false)
                $('.project-search-form select').prop('disabled', false)

                $('.property-search-form input').prop('disabled', true)
                $('.property-search-form select').prop('disabled', true)
            } else {
                $('.project-search-form').hide()
                $('.property-search-form').show()

                $('.project-search-form input').prop('disabled', true)
                $('.project-search-form select').prop('disabled', true)

                $('.property-search-form input').prop('disabled', false)
                $('.property-search-form select').prop('disabled', false)
            }
        })
        .on('click', '[data-bb-toggle="add-to-wishlist"]', (e) => {
            e.preventDefault()

            const $currentTarget = $(e.currentTarget)
            const id = $currentTarget.data('id')
            const cookieName = $currentTarget.data('type') === 'property' ? 'wishlist' : 'project_wishlist'

            const wishlist = decodeURIComponent(getCookie(cookieName) || '')
            const wishlistArray = wishlist ? wishlist.split(',') : []

            if (wishlistArray.includes(String(id))) {
                wishlistArray.splice(wishlistArray.indexOf(id), 1)
                $currentTarget.removeClass('active').html(`
                    <svg class="icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572"></path>
                    </svg>
                `)

                Theme.showSuccess($currentTarget.data('remove-message'))
            } else {
                wishlistArray.push(id)
                $currentTarget.addClass('active').html(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6.979 3.074a6 6 0 0 1 4.988 1.425l.037 .033l.034 -.03a6 6 0 0 1 4.733 -1.44l.246 .036a6 6 0 0 1 3.364 10.008l-.18 .185l-.048 .041l-7.45 7.379a1 1 0 0 1 -1.313 .082l-.094 -.082l-7.493 -7.422a6 6 0 0 1 3.176 -10.215z" />
                    </svg>
                `)

                Theme.showSuccess($currentTarget.data('add-message'))
            }

            setCookie(cookieName, wishlistArray.join(','), 365)
            initWishlistCount()
        })
        .on('click', '[data-bb-toggle="toggle-filter-offcanvas"]', (e) => {
            e.preventDefault()

            $('.search-box-offcanvas').toggleClass('active')
        })
        .on('click', '.search-box-offcanvas-backdrop', (e) => {
            $('.search-box-offcanvas').removeClass('active')
        })

    $(`[data-bb-toggle="change-search-type"][data-value="${$('.flat-tab').find('form input[name="type"]')}"]`).trigger(
        'click'
    )

    document.addEventListener('shortcode.loaded', (e) => {
        const {name, html, attributes} = e.detail

        switch (name) {
            case 'image-slider':
                initImageSlider()

                break

            case 'testimonials':
                initTestimonials()

                break

            case 'location':
                initLocation()

                break

            case 'properties':
                initWishlist()

                if (attributes.style === '2') {
                    initPropertiesTab()
                }

                if (attributes.style === '7') {
                    initProperties()
                }

                break

            case 'property-categories':
                initPropertyCategories()

                break

            case 'services':
                initServices()

                break
        }
    })

    if ($("[data-countdown]").length > 0) {
        const $element = $("[data-countdown]")

        $element.countdown($element.data('date'), function (event) {
            $element.find('[data-days]').text(event.strftime('%D'))
            $element.find('[data-hours]').text(event.strftime('%H'))
            $element.find('[data-minutes]').text(event.strftime('%M'))
            $element.find('[data-seconds]').text(event.strftime('%S'))
        });
    }

    /* Enhanced Location Dropdown with Infinite Scroll
    -------------------------------------------------------------------------------------*/
    const enhancedLocationDropdown = function() {
        let page = 1;
        let searchTerm = '';
        let loadingMore = false;
        let allItemsLoaded = false;

        // Function to set up the enhanced behavior after niceSelect is initialized
        function enhanceLocationDropdown() {
            const $locationNiceSelect = $('#location').next('.nice-select');
            if (!$locationNiceSelect.length) return;

            // Add search wrapper if it doesn't exist
            if (!$locationNiceSelect.find('.nice-select-search-wrapper').length) {
                const $list = $locationNiceSelect.find('.list');

                // Add search input at the top of the list
                $list.prepend('<div class="nice-select-search-wrapper"><input type="text" class="nice-select-search" placeholder="Search for a city..."/></div>');

                // Add loading indicator at the bottom of the list
                $list.append('<div class="nice-select-loader" style="display:none;"><div class="spinner"></div></div>');

                setupEventHandlers($locationNiceSelect);

                // Load cities (with delay to ensure DOM is ready)
                setTimeout(() => loadCities($locationNiceSelect), 100);
            }
        }

        function setupEventHandlers($niceSelect) {
            const $searchInput = $niceSelect.find('.nice-select-search');
            const $list = $niceSelect.find('.list');

            // Search input handler
            $searchInput.on('input', function() {
                searchTerm = $(this).val();
                page = 1;
                allItemsLoaded = false;

                // Clear existing options except the placeholder "All" option
                $list.find('.option:not(:first-child)').remove();
                $('#location').find('option:not(:first-child)').remove();

                loadCities($niceSelect);
            });

            // Scroll event for infinite loading
            $list.on('scroll', function() {
                const scrollPosition = $(this).scrollTop();
                const scrollHeight = $(this).prop('scrollHeight');
                const listHeight = $(this).height();

                // Load more when near bottom
                if (!loadingMore && !allItemsLoaded && (scrollPosition + listHeight > scrollHeight - 50)) {
                    page++;
                    loadCities($niceSelect, true);
                }
            });

            // Auto-focus search when dropdown opens
            $niceSelect.on('click', function(e) {
                if ($niceSelect.hasClass('open') && !$(e.target).hasClass('nice-select-search')) {
                    setTimeout(() => $searchInput.focus(), 10);
                }
            });

            // Prevent dropdown from closing when clicking search
            $searchInput.on('click', function(e) {
                e.stopPropagation();
            });
        }

        function loadCities($niceSelect, append = false, minimal = 1) {
            if (loadingMore) return;

            loadingMore = true;
            const $loader = $niceSelect.find('.nice-select-loader');
            $loader.show();

            $.ajax({
                url: window.location.origin + '/ajax/cities',
                type: 'GET',
                data: {
                    location: searchTerm,
                    page: page,
                    minimal: minimal,
                },
                success: function(response) {
                    let cities = [];

                    if (typeof response === 'string') {
                        // Parse HTML response (fallback)
                        const $tempElement = $('<div>').html(response);
                        $tempElement.find('.search-suggestion-item').each(function() {
                            const cityFullName = $(this).text().trim();
                            const cityId = $(this).data('value') || generateTempId(cityFullName);

                            cities.push({
                                id: cityId,
                                text: cityFullName,
                            });
                        });

                        allItemsLoaded = cities.length === 0 || cities.length < 10;
                    } else {
                        // Handle JSON response
                        if (response.data && Array.isArray(response.data.items)) {
                            cities = response.data.items;
                            allItemsLoaded = !response.data.has_more || cities.length === 0;
                        } else {
                            allItemsLoaded = true;
                        }
                    }

                    updateCityOptions($niceSelect, cities, append);
                },
                error: function() {
                    allItemsLoaded = true;
                },
                complete: function() {
                    loadingMore = false;
                    $loader.hide();
                },
            });
        }

        function updateCityOptions($niceSelect, cities, append) {
            const $select = $('#location');
            const $list = $niceSelect.find('.list');
            const $loader = $niceSelect.find('.nice-select-loader');

            if (!append) {
                // Clear existing options except first one (All)
                $list.find('.option:not(:first-child)').remove();
                $select.find('option:not(:first-child)').remove();
            }

            cities.forEach(function(city) {
                // Add to the original select
                $select.append(`<option value="${city.id}">${city.text}</option>`);

                // Add to nice select list before the loader
                $loader.before(`<li data-value="${city.id}" class="option">${city.text}</li>`);
            });

            // Re-attach click handlers to new items
            $list.find('.option').off('click').on('click', function(e) {
                e.stopPropagation();
                const val = $(this).data('value');
                const text = $(this).text();

                $select.val(val);
                $niceSelect.find('.current').text(text);

                $niceSelect.removeClass('open');
            });
        }

        function generateTempId(text) {
            let hash = 0;
            for (let i = 0; i < text.length; i++) {
                hash = ((hash << 5) - hash) + text.charCodeAt(i);
                hash |= 0;
            }
            return 'city_' + Math.abs(hash);
        }

        // Wait for nice-select to be initialized by the theme
        const checkInterval = setInterval(function() {
            if ($('#location').next('.nice-select').length) {
                clearInterval(checkInterval);
                enhanceLocationDropdown();
            }
        }, 100);

        // Backup initialization if the theme hasn't done it after 2 seconds
        setTimeout(function() {
            if (!$('#location').next('.nice-select').length && $.fn.niceSelect) {
                $('#location').niceSelect();
                enhanceLocationDropdown();
            }
        }, 2000);
    }

    enhancedLocationDropdown()
})
