<template>
    <section class="flex flex-col justify-center items-center p-3 md:p-10 md:py-20">
        <div
            class="md:w-3/4 lg:w-8/12 xl:w-5/12 bg-white ring-0 md:ring-1 lg:ring-1 xl:ring-1 rounded-md ring-waldorf-red p-5 md:p-20">
            <PageHeader page-headline="Anmeldung"
                        page-subtext="Willkommen bei der Freien Waldorfschule Augsburg! Bitte melden Sie sich an!"></PageHeader>
            <form @focusout="validate" @submit.prevent="submit"
                  class="rounded-lg mt-5 space-y-5">
                <TextInput :error-message="form.errors.samaccountname" label="Benutzername" v-model="form.samaccountname"/>
                <TextInput :error-message="form.errors.samaccountname" label="Passwort" type="Password"
                           v-model="form.password"/>
                <div class="w-full flex items-center justify-center">
                    <DefaultRedButton text="Anmelden"></DefaultRedButton>
                </div>
            </form>
        </div>
    </section>
</template>

<script lang="ts">
import PublicLayout from "@/Shared/PublicLayout.vue";
import {useForm} from "@inertiajs/inertia-vue3";
import {usePrevalidate} from "@/Composables/usePrevalidation";
import {Method} from "@inertiajs/inertia";
import FormError from "@/Components/Common/Error/FormError.vue";
import TextInput from "@/Components/Common/Form/TextInput.vue";
import DefaultRedButton from "@/Components/Common/Buttons/DefaultRedButton.vue";
import PageHeader from "@/Components/Common/Page/PageHeader.vue";
import {watch} from "vue";


export default {
    name: "Login",
    components: {PageHeader, DefaultRedButton, TextInput, FormError},
    layout: PublicLayout,
    setup() {

        const form = useForm({
            samaccountname: null,
            password: null
        })
        const {validate} = usePrevalidate(form, {
            method: Method.POST,
            url: "/oauth2/authorize"
        })

        function submit() {
            form.post('/oauth2/authorize')

        }

        return ({form, validate, submit})
    }


}
</script>


<style scoped>

::placeholder {
    @apply text-gray-400 font-karla-regular
}

</style>
