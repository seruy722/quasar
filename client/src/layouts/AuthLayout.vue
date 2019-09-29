<template>
    <div
        data-vue-component-name="AuthLayout"
    >
        <q-layout
            view="hHh LpR fFf"
            class="column justify-center align-center"
        >
            <q-page-container>
                <router-view/>
            </q-page-container>
        </q-layout>
    </div>
</template>

<script>
    export default {
        name: 'AuthLayout',
    };
</script>
