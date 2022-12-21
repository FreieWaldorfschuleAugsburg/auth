<template>
    <div class="flex flex-col gap-1">
        <label class="font-karla-bold text-body-large" for="username">{{ label }}</label>
        <input :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
               class="rounded-md p-2 bg-transparent border border-gray-300  appearance-none focus:border-none focus:outline-waldorf-red focus:ring-0 font-karla-semibold"
               :type="type" name="username" id="username">
        <FormError :message="errorMessage"/>
    </div>

</template>

<script lang="ts">
import FormError from "@/Components/Common/Error/FormError.vue";
import {watch} from "vue";

export default {
    name: "TextInput",
    components: {FormError},
    props: {
        modelValue: String,
        type: {
            default: "text",
            type: String
        },
        label: String,
        errorMessage: String
    },
    setup() {


    },


    emits: ['update:modelValue']
}
</script>

<style scoped>

</style>
