<template>
    <div
        data-vue-component-name="BaseInput"
    >
        <q-input
            ref="baseInput"
            v-model.trim="inputData.value"
            :dark="inputData.dark"
            :error-message="viewError()"
            :error="isError"
            :type="inputData.type"
            :label="$t(inputData.lang)"
            :dense="inputData.dense"
            :autofocus="inputData.autofocus"
            :mask="inputData.mask || ''"
            :unmasked-value="inputData.unmasked || false"
            :fill-mask="inputData.fillMask || false"
            filled
            bottom-slots
            clearable
            clear-icon="close"
            @input="changeErrors"
            @keyup="$emit('onKeyUp', $event)"
        >
            <template v-slot:prepend>
                <q-icon :name="inputData.icon" />
            </template>
        </q-input>
    </div>
</template>

<script>
    import ErrorsServerMixin from 'src/mixins/ViewErrors';

    export default {
        name: 'BaseInput',
        mixins: [ErrorsServerMixin],
        props: {
            inputData: {
                type: Object,
                default: () => ({}),
            },
            errors: {
                type: Object,
                default: () => ({}),
            },
        },
    };
</script>
