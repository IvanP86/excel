<template>
    <div class="w-96 mx-auto">
        Import
        <div class="flex justify-center">
            <form class="flex">
                <div class="mr-2">
                    <input class="w-16 rounded-full" type="number" min="1" max="2" v-model="type">
                </div>
                <div>
                    <input @change="setExcel" type="file" ref="file" class="hidden">
                    <a @click.prevent="selectExcel" href="#"
                        class="block rounded-full bg-green-600 w-32 text-center text-white p-2">Excel</a>
                </div>
            </form>
            <div v-if="file" class="ml-3">
                <a @click.prevent="importExcel" href="#"
                    class="block rounded-full bg-sky-600 w-32 text-center text-white p-2">Import</a>
            </div>
            <slot />
        </div>
        <div v-if="$page.props.flash.message" class="mt-4 text-green-600 text-center">
            {{ $page.props.flash.message }}
        </div>
    </div>
</template>

<script>

import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    name: "Import",
    layout: MainLayout,
    data() {
        return {
            file: null,
            type: 1
        }
    },
    methods: {
        selectExcel() {
            this.$refs.file.click()
        },
        setExcel(e) {
            this.file = e.target.files[0]
            console.log(this.file)
        },
        importExcel() {
            const formData = new FormData()
            formData.append('file', this.file)
            formData.append('type', this.type)

            this.$inertia.post('/projects/import', formData, {
                onSuccess: () => {
                    this.file = null
                    this.$refs.file.value = null
                }
            })
        }
    }
}
</script>

<style scoped></style>