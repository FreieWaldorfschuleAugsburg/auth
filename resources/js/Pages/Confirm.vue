<template>
    <div class="flex flex-col p-10 py-20 items-center">

        <p class="font-sans font-bold text-4xl ">Bitte bestätigen Sie</p>
        <p class="font-sans font-bold text-4xl ">{{ props.client_name.name }} möchte Zugriff</p>

        <img class="h-60 mt-10" src="/assets/img/auth-draw.svg" alt="">

        <div class="w-full sm:w-4/6 lg:w-6/12 sm:w-4/6 lg:w-6/12 flex gap-2 justify-center mt-10 ">
            <form @submit.prevent="accept">
                <button type="submit" class="bg-blue-600 p-3 rounded text-white font-sans font-medium">Bestätigen
                </button>
            </form>

            <form @submit.prevent="deny">
                <button type="submit" class="bg-blue-600 p-3 rounded text-white font-sans font-medium">Ablehnen</button>
            </form>


        </div>
    </div>


</template>

<script lang="ts">
import {reactive} from "vue";

export default {
    name: "Login",
}
</script>


<script setup lang="ts">

import {reactive} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    client_id: {type: Object},
    client_name: {type: String},
    redirect_uri: {type: String},
    response_type: {type: String},
    state: {type: String}
})
const acceptData = reactive({
    client_id: props.client_id,
    redirect_uri: props.redirect_uri,
    response_type: props.response_type,
    state: props.state
})

function accept() {
    Inertia.post('/auth/confirm', acceptData)
}

function deny() {
    Inertia.delete('/authorize')
}


</script>

<style scoped>

</style>
