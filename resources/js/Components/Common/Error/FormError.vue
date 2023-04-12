<template>
    <p class="text-waldorf-red font-karla-semibold text-sm ">{{ message }}</p>
</template>

<script setup lang="ts">
const props = defineProps<{ message: string | undefined }>();
</script>

<style scoped>

</style>
