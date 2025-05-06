<template>
    <slot v-bind="{ data, account, isLoading, isSubscribing, postSubscribe }"></slot>
</template>

<script>
export default {
    data: function () {
        return {
            isLoading: true,
            isSubscribing: false,
            data: [],
            account: {},
            currentPackageId: null,
        }
    },

    mounted() {
        this.getData()
    },

    props: {
        ajaxUrl: {
            type: String,
            required: true,
        },
        subscribeUrl: {
            type: String,
            required: true,
        },
    },

    methods: {
        getData() {
            this.data = []
            this.isLoading = true
            axios.get(this.ajaxUrl).then((res) => {
                if (res.data.error) {
                    Botble.showError(res.data.message)
                } else {
                    this.data = res.data.data.packages
                    this.account = res.data.data.account
                    const headerAccountCredit = document.querySelector('.account-current-credit span')
                    if (headerAccountCredit) {
                        headerAccountCredit.innerText = this.account.formatted_credits
                    }
                }
                this.isLoading = false
            })
        },

        postSubscribe(id) {
            this.isSubscribing = true
            this.currentPackageId = id
            axios
                .post(this.subscribeUrl, { id: id, _method: 'PUT' })
                .then((res) => {
                    if (res.data.error) {
                        Botble.showError(res.data.message)
                    } else {
                        if (res.data.data && res.data.data.next_page) {
                            window.location.href = res.data.data.next_page
                        } else {
                            this.account = res.data.data
                            Botble.showSuccess(res.data.message)
                            this.getData()
                        }
                    }
                    this.isSubscribing = false
                })
                .catch((error) => {
                    this.isSubscribing = false
                    console.log(error)
                })
        },
    },
}
</script>
