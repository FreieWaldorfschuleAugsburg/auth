<template>
    <div>
        <p class="text-title-medium md:text-title-large font-karla-bold"><span class="text-waldorf-red">{{ clientName }}</span> kann
            dadurch:</p>
        <ul class="list-disk text-gray-600 font-karla-regular text-body-medium">
            <li v-for="parsedScope in parsedScopes" :key="parsedScope">{{ scopeTranslations[parsedScope.toLowerCase()] }}</li>
        </ul>

    </div>


</template>

<script setup lang="ts">
import {defineProps} from "vue"
import {scopeTranslations} from "@/translations/scopeTranslations.ts";

const props = defineProps<{
    clientName: string,
    scopes?: string
}>()


const parsedScopes: Array<string> = props.scopes.split(" ");
console.log(parsedScopes);

</script>

<style scoped>

</style>
