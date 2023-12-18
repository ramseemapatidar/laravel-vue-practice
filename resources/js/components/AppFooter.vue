<script setup>
import { useAuthUserStore } from '../stores/AuthUserStore';
const authUseStore = useAuthUserStore();

defineProps({

    settings:Object
})
</script>
<template>
    <footer class="main-footer">

        <div class="float-right d-none d-sm-inline">
            {{authUseStore.user.name}}
        </div>

        <strong>Copyright &copy; 2023 <a href="https://adminlte.io">{{settings?.app_name}}</a>.</strong> All rights
        reserved.
    </footer>
</template>
