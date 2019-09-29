<template>
    <div
        data-vue-component-name="BaseSelect"
    >
        <q-select
            v-model="modelData"
            filled
            :options="options"
            :label="$t(inputData.label)"
            :error-message="viewError()"
            :error="isError"
            emit-value
            map-options
            @input="changeErrors"
        >
            <template v-slot:prepend>
                <q-icon :name="inputData.icon" />
            </template>
        </q-select>
    </div>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'BaseSelect',
        mixins: [ErrorsServerMixin],
        props: {
            inputData: {
                type: Object,
                default: () => ({}),
            },
            value: {
                type: [String, Number],
                default: '',
            },
            options: {
                type: Array,
                default: () => [],
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
        computed: {
            modelData: {
                get: function getData() {
                    return this.value;
                },
                set: function setData(newValue) {
                    this.$emit('update:value', newValue);
                },
            },
        },
    };
</script>
