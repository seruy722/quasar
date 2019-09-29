<template>
    <div
        data-vue-component-name="Customers"
    >
        <Table :tableData="data">
            <template v-slot:inner-body="{props}">
                <q-tr :props="props">
                    <q-td auto-width>
                        <q-checkbox v-model="props.selected" :dense="$q.screen.xs || $q.screen.sm" />
                    </q-td>
                    <q-td key="code" :props="props">
                        {{ props.row.code }}

                        <Icon
                            :name="props.expand ? 'arrow_drop_up' : 'arrow_drop_down'"
                            :size="$q.screen.xs"
                            :tooltip="props.expand ? 'hide' : 'reveal'"
                            @iconClick="props.expand = !props.expand"
                        />
                    </q-td>
                    <q-td key="user" :props="props">{{ props.row.user ? props.row.user.name : '' }}</q-td>
                </q-tr>
                <q-tr v-show="props.expand" :props="props">
                    <q-td colspan="100%">
                        <q-table
                            title="Treats"
                            :data="props.row.customers"
                            :columns="columns"
                            row-key="name"
                        />
                    </q-td>
                </q-tr>
            </template>
        </Table>
    </div>
</template>

<script>
    import { getUrl } from 'src/tools/url';

    export default {
        name: 'Customers',
        components: {
            Table: () => import('src/components/Table.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
        },
        data() {
            return {
                data: {
                    data: [],
                    title: this.$t('codes'),
                    columns: [
                        {
                            name: 'code',
                            required: true,
                            label: this.$t('code'),
                            align: 'center',
                            field: 'code',
                            sortable: true,
                        },
                        {
                            name: 'user',
                            label: this.$t('customer'),
                            field: 'user',
                            align: 'center',
                            sortable: true,
                        },
                    ],
                    visibleColumns: ['user', 'name'],
                    viewBody: true,
                    selected: [],
                },
                columns: [
                    {
                        name: 'name',
                        required: true,
                        label: 'Name',
                        align: 'left',
                        field: 'name',
                        sortable: true,
                    },
                    {
                        name: 'phone',
                        label: 'Phone',
                        field: 'phone',
                        align: 'left',
                        sortable: true,
                    },
                ],
            };
        },
        mounted() {
            this.$axios.get(getUrl('codes'))
              .then((response) => {
                  this.data.data = _.get(response, 'data.data');
              });
            devlog.log('dense', this.$q.screen);
        },
    };
</script>
