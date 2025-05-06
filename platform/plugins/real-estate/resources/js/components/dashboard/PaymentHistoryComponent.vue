<template>
    <slot v-bind="{ isLoading, isLoadingMore, data, getData }"></slot>
</template>

<script>
export default {
    props: {
        ajaxUrl: {
            type: String,
            required: true,
        },
    },

    data() {
        return {
            isLoading: true,
            isLoadingMore: false,
            data: [],
            nextUrl: null
        }
    },

    mounted() {
        this.getData()
    },

    methods: {
        getData(url = null) {
            if (url) {
                this.isLoadingMore = true
            } else {
                this.isLoading = true
            }

            axios.get(url || this.ajaxUrl).then((res) => {
                let oldData = []

                if (this.data.data) {
                    oldData = this.data.data
                }
                this.data = res.data
                this.data.data = oldData.concat(this.data.data)
                this.isLoading = false
                this.isLoadingMore = false
            })
        },
    },
}
</script>
