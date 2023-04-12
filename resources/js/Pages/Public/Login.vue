<template>
    <section class="flex flex-col justify-center items-center p-3 md:p-10 md:py-20">
        <div
            class="md:w-3/4 lg:w-8/12 xl:w-5/12 bg-white ring-0 md:ring-1 lg:ring-1 xl:ring-1 rounded-md ring-waldorf-red p-5 md:p-20">
            <PageHeader :page-headline="useTranslation('login.header')"
                        :page-subtext="useTranslation('login.subtext')"></PageHeader>
            <form @submit.prevent="form.submit('post', '/login')"
                  class="rounded-lg mt-5 space-y-5">
                <TextInput :error-message="form.errors.username ? useTranslation(form.errors.username) : undefined"
                           v-model="form.username" :label="useTranslation('login.labels.username')" />
                <TextInput :error-message="form.errors.password ? useTranslation(form.errors.password) : undefined"
                           v-model="form.password" :label="useTranslation('login.labels.password')" type="password"
                />
                <p class="text-sm text-waldorf-red -mt-2">{{ usePage().props.errors?.auth ? useTranslation(usePage().props.errors.auth) : undefined}}</p>
                <div class="w-full flex items-center justify-center">
                    <DefaultRedButton type="submit"
                                      :text="useTranslation('login.submit')"></DefaultRedButton>
                </div>
            </form>
        </div>
    </section>
</template>

<script setup lang="ts">


import DefaultRedButton from "../../Components/Common/Buttons/DefaultRedButton.vue";
import TextInput from "../../Components/Common/Form/TextInput.vue";
import PageHeader from "../../Components/Common/Page/PageHeader.vue";
import { useTranslation } from "../../Mixins/translations";
import { useForm, usePage } from "@inertiajs/vue3";
import PublicLayout from "../../Shared/PublicLayout.vue";

defineOptions({ layout: PublicLayout });

const form = useForm({
    username: "",
    password: ""
});


</script>

<style scoped>

</style>
