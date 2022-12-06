<template>
    <div class="flex items-center justify-center flex-col px-96 py-20">

        <div class="rounded-xl border border-gray-300/50 flex flex-col w-full">


            <img class="h-60 mt-10" src="/assets/img/auth-illustration.svg" alt="">
            <div class="">
                <div class="">
                    <form @submit.prevent="submit" class="px-5 py-8 rounded-lg mt-5 space-y-5">
                        <div class="flex flex-col gap-1 text-white">

                            <input
                                class="rounded p-1.5 lg:p-3 bg-transparent border-gray-300 border border-gray-600 outline-none focus:outline-none text-white" placeholder="Benutzername"
                                type="text" name="username" id="username" v-model="form.samaccountname">
                        </div>
                        <div class="flex flex-col gap-1 font-inter-medium">
                            <input
                                class="rounded p-1.5 lg:p-3 bg-transparent border-gray-300 border border-gray-600 outline-none focus:outline-none text-white" placeholder="Passwort"
                                type="password" name="password" id="password" v-model="form.password">
                        </div>

                        <div class="flex flex-col">
                            <button type="submit"
                                    class="bg-blue-600 text-white p-2.5 lg:p-4 rounded font-sans text-lg ">
                                Anmelden
                            </button>
                        </div>

                    </form>
                </div>
            </div>

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

const form = reactive({
    samaccountname: null,
    password: null,
})

function submit() {
    Inertia.post('/oauth2/authorize', form)
}

</script>

<style scoped>

::placeholder{
    @apply text-gray-400
}

</style>
