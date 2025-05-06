<template>
    <slot v-bind="{ activityLogs, loading }"></slot>
</template>

<script>
export default {
    props: {
        ajaxUrl: {
            type: String,
            required: true,
        }
    },

    data() {
        return {
            loading: true,
            activityLogs: [],
        }
    },

    mounted() {
        this.getActivityLogs()
    },

    methods: {
        getActivityLogs(url) {
            this.loading = true

            axios.get(url || this.ajaxUrl).then((res) => {
                let oldData = []

                if (this.activityLogs.data) {
                    oldData = this.activityLogs.data
                }

                this.activityLogs = res.data
                this.activityLogs.data = oldData.concat(this.activityLogs.data)
                this.loading = false
            })
        },
    },
}
</script>
