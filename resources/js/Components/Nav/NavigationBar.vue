<template>
    <div class="flex bg-waldorf-red w-full px-5 py-3 items-center justify-between">
        <div class="flex gap-3">
            <p class="text-white font-karla font-semibold text-headline-small">Auth - Freie Waldorfschule Augsburg</p>
        </div>
        <div>
           <button class="bg-white px-5 py-2 rounded font-medium shadow shadow-md" @click="router().get('/login')">Login </button>
        </div>
    </div>
</template>

<script>
import { router } from "@inertiajs/vue3";

export default {
    name: "NavigationBar",
    methods: {
        router() {
            return router
        }
    }
}
</script>

<style scoped>

</style>
