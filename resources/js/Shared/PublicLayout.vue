<template>
<div>
    <NavigationBar></NavigationBar>
    <slot/>
</div>



</template>

<script>
import NavigationBar from "@/Components/Nav/NavigationBar.vue";

export default {
    name: "PublicLayout",
    components: {NavigationBar}
}
</script>

<style scoped>

</style>
