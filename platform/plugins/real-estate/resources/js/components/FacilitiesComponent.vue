<template>
    <slot v-bind="{ items, facilities, addRow, deleteRow, removeSelectedItem }" />
</template>

<script>
const { nextTick } = Vue

export default {
    data: function () {
        return {
            items: [],
        }
    },
    mounted() {
        if (this.selected_facilities.length) {
            this.items = []
            for (const item of this.selected_facilities) {
                this.items.push({ id: item.id, distance: item.distance })
            }
        }
    },
    props: {
        selected_facilities: {
            type: Array,
            default: () => [],
        },
        facilities: {
            type: Array,
            default: () => [],
        },
    },

    methods: {
        addRow: function () {
            this.items.push({ id: '', distance: '' })

            nextTick(() => {
                if (window.Botble) {
                    window.Botble.initResources()
                }
            })
        },
        deleteRow: function (index) {
            this.items.splice(index, 1)
        },
        removeSelectedItem: function () {
            for (let i = 0; i < this.facilities.length; i++) {
                for (const item of this.items) {
                    if (item.id === this.facilities[i].id) {
                        this.facilities.slice(i, 1)
                    }
                }
            }
        },
    },
}
</script>
