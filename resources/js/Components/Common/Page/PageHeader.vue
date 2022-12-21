<template>
    <p class="font-karla-bold text-black text-headline-large">{{ pageHeadline }}</p>
    <p class="font-karla-medium text-body-medium text-gray-600">{{ pageSubtext }}</p>
</template>

<script>
export default {
    name: "PageHeader",
    props: {
        pageHeadline: String,
        pageSubtext: String,
    },
};
</script>

<style scoped></style>
