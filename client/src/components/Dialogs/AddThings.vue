<template>
    <div
        data-vue-component-name="AddThings"
    >
<!--        <slot name="button">-->
<!--            <IconBtn-->
<!--                color="positive"-->
<!--                icon="add_box"-->
<!--                :tooltip="$t('add')"-->
<!--                @iconBtnClick="dialog(true)"-->
<!--            />-->
<!--        </slot>-->

        <Dialog
            :dialog.sync="show"
            :persistent="true"
            title="Опись вложения"
        >
            <template v-slot:body>
                <ScrollArea>
                    <div class="fit row wrap justify-start items-center content-start bg-white">
                        <div
                            v-for="(item, index) in thing"
                            :key="index"
                            class="col-xs-12 col-sm-4 col-md-3 col-lg-2 q-pt-md q-px-sm"
                        >
                            <BaseInput
                                v-model.trim="item.value"
                                :label="item.label"
                                :type="item.type"
                                :mask="item.mask"
                                :autofocus="item.autofocus"
                                :dense="$q.screen.xs || $q.screen.sm"
                                :field="index"
                                :errors="errorsData"
                            />
                        </div>

                        <div class="q-px-sm">
                            <OutlineBtn
                                :label="$t('add')"
                                color="positive"
                                icon="save"
                                :size="size"
                                @clickOutlineBtn="checkErrors(thing, add)"
                            />
                        </div>
                    </div>

                    <List
                        dense
                        bordered
                        padding
                        separator
                    >
                        <ListItem>
                            <ItemSection>Название</ItemSection>
                            <ItemSection>Количество</ItemSection>
                            <ItemSection>Действие</ItemSection>
                        </ListItem>

                        <ListItem
                            v-for="(item, index) in localThings"
                            :key="index"
                            v-ripple
                            clickable
                        >
                            <ItemSection>{{ item.title }}</ItemSection>
                            <ItemSection>{{ item.count }}</ItemSection>
                            <ItemSection>
                                <IconBtn
                                    color="negative"
                                    icon="delete"
                                    :dense="$q.screen.xs || $q.screen.sm"
                                    :tooltip="$t('delete')"
                                    @iconBtnClick="remove(index)"
                                />
                            </ItemSection>
                        </ListItem>
                    </List>
                </ScrollArea>
            </template>
            <template v-slot:actions>
                <div
                    class="row justify-end"
                    style="width: 100%;"
                >
                    <div class="q-px-sm">
                        <OutlineBtn
                            :label="$t('clear')"
                            color="negative"
                            icon="clear"
                            :size="size"
                            @clickOutlineBtn="clear(thing)"
                        />
                    </div>

                    <div class="q-px-sm">
                        <OutlineBtn
                            :label="$t('save')"
                            color="positive"
                            icon="save"
                            :size="size"
                            @clickOutlineBtn="save"
                        />
                    </div>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script>
    import CheckErrorsMixin from 'src/mixins/CheckErrors';
    // import { mapGetters } from 'vuex';

    export default {
        name: 'AddThings',
        components: {
            Dialog: () => import('src/components/Dialogs/Dialog.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            OutlineBtn: () => import('src/components/Buttons/OutlineBtn.vue'),
            BaseInput: () => import('src/components/Elements/BaseInput.vue'),
            ScrollArea: () => import('src/components/ScrollArea.vue'),
            List: () => import('src/components/Elements/List/List.vue'),
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
        },
        mixins: [CheckErrorsMixin],
        props: {
            things: {
                type: String,
                default: '',
            },
            showDialog: {
                type: Boolean,
                default: false,
            },
        },
        data() {
            return {
                show: false,
                localThings: [],
                thing: {
                    title: {
                        type: 'text',
                        label: this.$t('title'),
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Максимальное количество символов 255.',
                                options: {
                                    min: undefined,
                                    max: 255,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        autofocus: true,
                        default: '',
                        value: '',
                    },
                    count: {
                        type: 'number',
                        label: this.$t('quantity'),
                        rules: [
                            {
                                name: 'isLength',
                                error: 'Максимальное количество символов 10.',
                                options: {
                                    min: undefined,
                                    max: 10,
                                },
                            },
                        ],
                        require: true,
                        requireError: 'Поле обьзательное для заполнения.',
                        default: 0,
                        value: 0,
                    },
                },
            };
        },
        computed: {
            size() {
                const {
                    sm,
                    xs,
                    md,
                    lg,
                } = this.$q.screen;

                let size = '';
                if (sm) {
                    size = 'sm';
                } else if (xs) {
                    size = 'xs';
                } else if (md) {
                    size = 'md';
                } else if (lg) {
                    size = 'lg';
                }
                return size;
            },
        },
        watch: {
            showDialog(val) {
                devlog.log('this.things', this.things);
                if (val) {
                    this.dialog(true);
                }
            },
            show(val) {
                if (val) {
                    this.setLocalThings(this.things);
                }
                this.$emit('update:showDialog', val);
            },
        },
        methods: {
            setLocalThings(val) {
                if (val && _.isString(val)) {
                    this.localThings = JSON.parse(val);
                } else {
                    this.localThings = [];
                }
            },
            add({ count, title }) {
                this.localThings.unshift({
                    count: count.value,
                    title: _.upperFirst(title.value),
                });
            },
            save() {
                if (_.isEmpty(this.localThings)) {
                    this.$emit('update:things', '');
                } else {
                    this.$emit('update:things', JSON.stringify(this.localThings));
                }
                this.dialog(false);
            },
            clear(data) {
                _.forEach(data, (item) => {
                    item.value = item.default;
                });
                this.localThings = [];
                this.$emit('update:things', '');
            },
            remove(index) {
                this.localThings.splice(index, 1);
            },
            dialog(val) {
                this.show = val;
            },
        },
    };
</script>
