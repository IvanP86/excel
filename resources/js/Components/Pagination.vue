<template>
    <div v-if="meta.links.length > 0">
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6">
            <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                <div>
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <template v-for="link in meta.links">
                            <Link v-if="link.url" :href="link.url"
                                :class="['relative inline-flex items-center px-4 py-2 text-sm focus:z-20  font-semibold', link.active ? 'z-10 bg-indigo-600 text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600' : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50  focus:outline-offset-0']"
                                v-html="link.label">
                            </Link>
                        </template>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3'
export default {
    name: "Pagination",

    components: {
        Link
    },

    props: [
        'meta'
    ]
}
</script>

<style scoped></style>