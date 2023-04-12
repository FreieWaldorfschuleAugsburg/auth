<template>
    <div class="flex flex-col gap-1 font-karla">
        <label class="font-bold text-body-large" for="username">{{ label }}</label>
        <input :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
               class="rounded-md p-2 bg-transparent border border-gray-300  appearance-none focus:border-none focus:outline-waldorf-red focus:ring-0 font-semibold"
               :type="type" :name="label" :id="label">
        <FormError :message="errorMessage" />
    </div>

</template>

<script lang="ts">
import FormError from "../Error/FormError.vue";

export default {
    name: "TextInput",
    components: { FormError },
    props: {
        modelValue: String,
        type: {
            default: "text",
            type: String
        },
        label: String,
        errorMessage: String
    },
    emits: ["update:modelValue"]
};
</script>

<style scoped>

</style>
