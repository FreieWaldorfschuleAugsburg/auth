<template>
    <div class="w-full justify-center items-center flex rounded-md p-5 md:p-20">
        <div class="w-full items-center flex rounded-md shadow p-10">
            <section class="text-left">
                <p class="font-karla font-semibold text-title-large">{{ useTranslation("notAvailable") }}</p>
                <p class="font-karla">{{useTranslation("notAvailableDescription")}}</p>
            </section>
        </div>
    </div>


</template>
<script setup lang="ts">
import PublicLayout from "../../Shared/PublicLayout.vue";
import { useTranslation } from "../../Mixins/translations";

defineOptions({ layout: PublicLayout });
</script>

<style scoped>

</style>
