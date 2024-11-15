<template>
        Index Task

        <div v-if="tasks" class="mt-4 -mb-3">
            <div class="not-prose relative bg-slate-50 rounded-xl overflow-hidden dark:bg-slate-800/25">
                <div class="absolute inset-0 bg-grid-slate-100 [mask-image:linear-gradient(0deg,#fff,rgba(255,255,255,0.6))] dark:bg-grid-slate-700/25 dark:[mask-image:linear-gradient(0deg,rgba(255,255,255,0.1),rgba(255,255,255,0.5))]"
                    style="background-position: 10px 10px;"></div>
                <div class="relative rounded-xl overflow-auto">
                    <div class="shadow-sm overflow-hidden my-8">
                        <table class="border-collapse table-auto w-full text-sm">
                            <thead>
                                <tr>
                                    <th
                                        class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                        User</th>
                                    <th
                                        class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                        File</th>
                                    <th
                                        class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800">
                                <tr v-for="task in tasks">
                                    <td
                                        class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                        {{ task.user.data.name }}</td>
                                    <td
                                        class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                                        {{ task.file.data.path }}</td>
                                    <td
                                        class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 dark:text-slate-400">
                                        {{ task.status }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="absolute inset-0 pointer-events-none border border-black/5 rounded-xl dark:border-white/5">
                </div>
            </div>
        </div>
        <slot />
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue'

export default {
    name: "Index",
    layout: MainLayout,

    props: [
        'tasks'
    ]
}
</script>

<style scoped></style>