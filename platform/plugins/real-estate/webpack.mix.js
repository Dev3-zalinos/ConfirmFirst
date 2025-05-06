const mix = require('laravel-mix')
const path = require('path')

const directory = path.basename(path.resolve(__dirname))
const source = `platform/plugins/${directory}`
const dist = `public/vendor/core/plugins/${directory}`

mix
    .sass(`${source}/resources/sass/dashboard/style.scss`, `${dist}/css/dashboard`)
    .sass(`${source}/resources/sass/dashboard/style-rtl.scss`, `${dist}/css/dashboard`)
    .sass(`${source}/resources/sass/real-estate.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/review.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/currencies.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/account-admin.scss`, `${dist}/css`)
    .sass(`${source}/resources/sass/front-auth.scss`, `${dist}/css`)
    .js(`${source}/resources/js/components.js`, `${dist}/js`)
    .js(`${source}/resources/js/real-estate.js`, `${dist}/js`)
    .js(`${source}/resources/js/currencies.js`, `${dist}/js`)
    .js(`${source}/resources/js/global-custom-fields.js`, `${dist}/js`)
    .js(`${source}/resources/js/custom-fields.js`, `${dist}/js`)
    .js(`${source}/resources/js/account-admin.js`, `${dist}/js`)
    .js(`${source}/resources/js/coupon.js`, `${dist}/js`)
    .js(`${source}/resources/js/app.js`, `${dist}/js`)
    .js(`${source}/resources/js/bulk-import.js`, `${dist}/js`)
    .js(`${source}/resources/js/export.js`, `${dist}/js`)
    .js(`${source}/resources/js/duplicate-property.js`, `${dist}/js`)
    .js(`${source}/resources/js/setting.js`, `${dist}/js`)
    .js(`${source}/resources/js/front-review.js`, `${dist}/js`)
    .js(`${source}/resources/js/custom-field.js`, `${dist}/js`)

if (mix.inProduction()) {
    mix
        .copyDirectory(`${dist}/js`, `${source}/public/js`)
        .copyDirectory(`${dist}/css`, `${source}/public/css`)
}
