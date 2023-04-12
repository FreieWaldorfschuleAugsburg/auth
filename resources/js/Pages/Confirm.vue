<template>
    <section class="flex flex-col justify-center items-center p-3 md:p-10 md:py-20">
        <div class="md:w-3/4 lg:w-8/12 xl:w-5/12 bg-white ring-0 md:ring-1 lg:ring-1 xl:ring-1 rounded-md ring-waldorf-red p-5 md:p-20">
            <PageHeader
                page-headline="Bestätigung"
                :page-subtext="subText"></PageHeader>
            <ClientPermissions class="mt-5" :client-name="client_name" :scopes="scope"></ClientPermissions>
            <div class="mt-10">
                <div class="w-full flex flex-col md:flex-row-reverse gap-3">
                    <form @submit.prevent="accept" class="w-full">
                        <DefaultRedButton text="Anmeldung bestätigen"/>
                    </form>

                    <form @submit.prevent="deny" class="w-full">
                        <DefaultButton text="Anmeldung ablehnen"></DefaultButton>
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
    layout: PublicLayout,
};
</script>

<script setup lang="ts">
import {reactive} from "vue";
import PageHeader from "@/Components/Common/Page/PageHeader.vue";
import ClientPermissions from "@/Components/Confirmation/ClientPermissions.vue";
import DefaultRedButton from "@/Components/Common/Buttons/DefaultRedButton.vue";
import DefaultButton from "@/Components/Common/Buttons/DefaultButton.vue";
import { router } from "@inertiajs/vue3";


const props = defineProps({
    client_id: {type: Object},
    client_name: {type: String},
    scope: {type: String},
    redirect_uri: {type: String},
    response_type: {type: String},
    state: {type: String},
});

const acceptData = reactive({
    client_id: props.client_id,
    scope: props.scope,
    redirect_uri: props.redirect_uri,
    response_type: props.response_type,
    state: props.state,
});


const subText = `Willkommen bei der Freien Waldorfschule Augsburg! Bitte bestätigen Sie,
    sich bei ${props.client_name} anzumelden!`


function accept() {
    router.post("/oauth2/confirm", acceptData);
}

function deny() {
    router.delete("/oauth2/confirm");
}
</script>

<style scoped></style>
