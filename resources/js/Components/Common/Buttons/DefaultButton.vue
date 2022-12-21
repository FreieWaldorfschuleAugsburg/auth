<template>
    <button :type="type"
            class="w-full text-waldorf-red p-3 rounded-md font-karla-bold text-body-large transition ease-in-out hover:scale-95 ">
        {{text}}
    </button>
</template>

<script lang="ts">
export default {
    name: "DefaultButton",
    props: {
        text: String,
        type: {
            type: String,
            default: "submit"
        }
    }
}
</script>

<style scoped>

</style>
