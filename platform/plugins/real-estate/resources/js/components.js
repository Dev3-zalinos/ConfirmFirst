import ActivityLogComponent from './components/dashboard/ActivityLogComponent'
import PackagesComponent from './components/dashboard/PackagesComponent'
import PaymentHistoryComponent from './components/dashboard/PaymentHistoryComponent'
import FacilitiesComponent from './components/FacilitiesComponent'

if (typeof vueApp !== 'undefined') {
    vueApp.booting((vue) => {
        vue.component('activity-log-component', ActivityLogComponent)
        vue.component('packages-component', PackagesComponent)
        vue.component('payment-history-component', PaymentHistoryComponent)
        vue.component('facilities-component', FacilitiesComponent)
    })
}
