<template>
    <div class="w-96 mx-auto">
        Index
        <slot/>
    </div>
</template>

<script>
import MainLayout from '@/Layouts/MainLayout.vue';

export default {
    name: "Index",
    layout: MainLayout
}
</script>

<style scoped>

</style>