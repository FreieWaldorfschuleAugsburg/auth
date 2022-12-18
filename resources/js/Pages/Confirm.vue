<template>
    <section class="flex mt-10 flex-col justify-center items-center p-3 md:p-10">
        <div class="md:w-3/4 bg-white p-5 md:p-20 rounded-3xl shadow shadow-xl shadow-waldorf-red-light">
            <p class="font-karla-extra-bold text-headline-large text-center">Bestätigung</p>
            <p class="font-karla-medium text-title-medium text-gray-600 text-center u">Herzlich willkommen bei der Freien Waldorfschule Augsburg! Bitte bestätigen Sie, sich für {{props.client_name}} anzumelden.</p>
            <div
                  class=" mt-10">
                <div class="w-full flex gap-3">
                    <form @submit.prevent="accept" class="w-full">
                        <button type="submit" class=" w-full bg-waldorf-red text-white p-3 rounded-xl font-karla-bold transition ease-in-out hover:scale-95 hover:bg-waldorf-red-dark">Bestätigen
                        </button>
                    </form>

                    <form @submit.prevent="deny" class="w-full">
                        <button type="submit" class="w-full bg-waldorf-red text-white p-3 rounded-xl font-karla-bold transition ease-in-out hover:scale-95 hover:bg-waldorf-red-dark">Ablehnen</button>
                    </form>

                </div>
            </div>
        </div>
    </section>
</template>

<script lang="ts">
import {reactive} from "vue";
import PublicLayout from "@/Shared/PublicLayout.vue";

export default {
    name: "Login",
    layout: PublicLayout
}
</script>


<script setup lang="ts">

import {reactive} from "vue";
import {Inertia} from "@inertiajs/inertia";

const props = defineProps({
    client_id: {type: Object},
    client_name: {type: String},
    scope: {type: String},
    redirect_uri: {type: String},
    response_type: {type: String},
    state: {type: String}
})


const acceptData = reactive({
    client_id: props.client_id,
    scope: props.scope,
    redirect_uri: props.redirect_uri,
    response_type: props.response_type,
    state: props.state,
})

function accept() {
    Inertia.post('/oauth2/confirm', acceptData)
}

function deny() {
    Inertia.delete('/oauth2/confirm')
}


</script>

<style scoped>

</style>
