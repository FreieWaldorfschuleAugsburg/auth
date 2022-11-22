<template>
    <div class="flex flex-col p-10 py-20 items-center">

        <p class="font-sans font-bold text-4xl ">Bitte melden Sie sich an</p>

            <img class="h-60 mt-10" src="/assets/img/auth-draw.svg" alt="">

            <div class="w-full sm:w-4/6 lg:w-6/12 sm:w-4/6 lg:w-6/12">
                <div class="">
                    <form @submit.prevent="submit" class="px-5 py-8 rounded-lg mt-5 space-y-5">
                        <div class="flex flex-col gap-1">
                            <label class="font-ser font-medium text-gray-800 text-lg"
                                   for="username">Benutzername</label>
                            <input
                                class="rounded p-1.5 lg:p-3 bg-transparent border-gray-300 border border-gray-600 outline-none focus:outline-none"
                                type="text" name="username" id="username" v-model="form.samaccountname">
                        </div>
                        <div class="flex flex-col gap-1 font-inter-medium">
                            <label class="font-ser font-medium text-gray-800 text-lg" for="password">Passwort</label>
                            <input
                                class="rounded p-1.5 lg:p-3 bg-transparent border-gray-300 border border-gray-600 outline-none focus:outline-none"
                                type="password" name="password" id="password" v-model="form.password">
                        </div>

                        <div class="flex flex-col">
                            <button type="submit"
                                    class="bg-blue-600 text-white p-2.5 lg:p-4 rounded font-sans text-lg font-medium">
                                Anmelden
                            </button>
                        </div>

                    </form>

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
    Inertia.post('/auth/login', form)
}

</script>

<style scoped>

</style>
