<template>
    <section class="flex mt-10 flex-col justify-center items-center p-3 md:p-10">
        <div class="md:w-3/4 bg-white p-5 md:p-10 rounded-3xl shadow shadow-xl shadow-waldorf-red-light">
            <p class="font-karla-extra-bold text-headline-large text-center">Anmeldung</p>
            <p class="font-karla-medium text-title-medium text-gray-600 text-center">Herzlich willkommen bei der Freien
                Waldorfschule Augsburg! Bitte melden Sie sich an.</p>
            <form @focusout="validate" @submit.prevent="submit"
                  class="rounded-lg mt-5 space-y-5">
                <div class="flex flex-col gap-1">
                    <label class="font-karla-medium text-gray-600 text-category lg:text-body" for="username">Benutzername</label>
                    <input
                        class="rounded-xl p-2 bg-transparent border border-gray-300  appearance-none focus:border-none focus:outline-waldorf-red focus:ring-0 font-karla-semibold"
                        type="text" name="username" id="username" v-model="form.samaccountname">
                    <FormError :message="form.errors.samaccountname" />
                </div>
                <div class="flex flex-col gap-1 font-inter-medium">
                    <label class="font-karla-medium text-gray-600 text-category lg:text-body"
                           for="password">Passwort</label>
                    <input
                        class="rounded-xl p-2 bg-transparent border border-gray-300 outline-none appearance-none focus:border-none focus:outline-waldorf-red focus:ring-0"
                        type="password" name="password" id="password" v-model="form.password">
                    <FormError :message="form.errors.password" />
                </div>

                <div class="flex flex-col">
                    <button type="submit"
                            class="bg-waldorf-red text-white p-3 rounded-xl font-karla-bold transition ease-in-out hover:scale-95 hover:bg-waldorf-red-dark">
                        Anmelden
                    </button>
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


export default {
    name: "Login",
    components: {FormError},
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
