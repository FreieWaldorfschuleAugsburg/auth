<template>
    <button :type="type"
            class="w-full bg-waldorf-red text-white p-3 rounded-md font-karla font-bold text-body-large transition ease-in-out hover:scale-95 hover:bg-waldorf-red-dark">
        {{text}}
    </button>
</template>

<script lang="ts">
export default {
    name: "DefaultRedButton",
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
