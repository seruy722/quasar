<template>
  <q-dialog
    v-model="dialog"
    persistent
    :maximized="maximizedToggle"
    transition-show="slide-up"
    transition-hide="slide-down"
    data-vue-component-name="DialogSendSms"
  >
    <q-card class="bg-primary text-white">
      <q-bar>
        <q-space />

        <q-btn dense flat icon="minimize" @click="maximizedToggle = false" :disable="!maximizedToggle">
          <q-tooltip v-if="maximizedToggle" content-class="bg-white text-primary">Minimize</q-tooltip>
        </q-btn>
        <q-btn dense flat icon="crop_square" @click="maximizedToggle = true" :disable="maximizedToggle">
          <q-tooltip v-if="!maximizedToggle" content-class="bg-white text-primary">Maximize</q-tooltip>
        </q-btn>
        <q-btn dense flat icon="close" v-close-popup>
          <q-tooltip content-class="bg-white text-primary">Close</q-tooltip>
        </q-btn>
      </q-bar>

      <q-card-section>
        <Table
          :table-properties="faxTableProperties"
          :table-data="values"
          :table-reactive-properties="faxTableReactiveProperties"
          :title="fax.name"
        >
          <template #top-buttons>
            <IconBtn
              color="positive"
              icon="explicit"
              tooltip="excel"
              @icon-btn-click="exportFaxData(faxTableReactiveProperties.selected)"
            />
          </template>

          <!--ОТОБРАЖЕНИЕ КОНТЕНТА НА МАЛЕНЬКИХ ЭКРАНАХ-->
          <template #inner-item="{props}">
            <div
              class="q-pa-xs col-xs-12 col-sm-6 col-md-4 col-lg-3 grid-style-transition"
              :style="props.selected ? 'transform: scale(0.95);' : ''"
            >
              <q-expansion-item
                expand-separator
                class="shadow-1 overflow-hidden"
                header-class="bg-secondary text-white"
                style="border-radius: 30px;border: 1px solid #26A69A;"
                expand-icon-class="text-white"
              >
                <template #header>
                  <q-item-section avatar>
                    <q-checkbox
                      v-model="props.selected"
                      dense
                    />
                  </q-item-section>

                  <q-item-section>
                    <q-item-label :lines="2">
                      {{ props.row.code_client_name }}
                    </q-item-label>
                  </q-item-section>
                </template>

                <q-list
                  separator
                  dense
                >
                  <q-item
                    v-for="col in props.cols.filter(col => col.name !== 'desc')"
                    :key="col.name"
                  >
                    <q-item-section>
                      <q-item-label>{{ `${col.label}:` }}</q-item-label>
                    </q-item-section>
                    <q-item-section side>
                      <q-item-label
                        v-if="col.field === 'things'"
                        :lines="10"
                      >
                        {{ col.value | thingsFilter }}
                      </q-item-label>
                      <q-item-label
                        v-else-if="col.field === 'kg'"
                      >
                        {{ col.value | numberFormatFilter }}
                      </q-item-label>
                      <q-item-label
                        v-else-if="col.field === 'notation'"
                        :lines="4"
                      >
                        {{ col.value }}
                      </q-item-label>
                      <q-item-label v-else>
                        {{ col.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section>
                      <BaseBtn
                        label="История"
                        color="info"
                        style="max-width: 100px;margin: 0 auto;"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-expansion-item>
            </div>
          </template>

          <template #inner-body="{props}">
            <q-tr
              :props="props"
              :class="{table__tr_bold_text: props.row.brand, 'cursor-pointer': !combineTableData}"
            >
              <q-td auto-width>
                <q-checkbox
                  v-model="props.selected"
                  dense
                />
              </q-td>

              <q-td
                key="code_client_name"
                class="cursor-pointer"
                :props="props"
              >
                {{ props.row.code_client_id }}
                <PopupEdit
                  v-if="combineTableData"
                  :value.sync="props.row.code_client_id"
                  type="number"
                  :title="props.row.code_client_name"
                  @add-to-save="addToAddSaveArray(props.row, 'code_client_id')"
                >
                  <SearchSelect
                    v-model="props.row.code_client_id"
                    label="Клиент"
                    :dense="$q.screen.xs || $q.screen.sm"
                    :options="clientCodes"
                  />
                </PopupEdit>
              </q-td>

              <q-td
                key="place"
                :props="props"
              >
                {{ props.row.place }}
              </q-td>

              <q-td
                key="kg"
                :props="props"
              >
                {{ props.row.kg | numberFormatFilter }}
              </q-td>

              <q-td
                key="for_kg"
                class="text-bold text-red cursor-pointer"
                :props="props"
              >
                {{ props.row.for_kg | numberFormatFilter }}
                <PopupEdit
                  v-if="combineTableData"
                  :value.sync="props.row.for_kg"
                  type="number"
                  :title="props.row.code_client_name"
                  @add-to-save="addToAddSaveArray(props.row, 'for_kg')"
                >
                  <q-input
                    v-model.number="props.row.for_kg"
                    type="number"
                    autofocus
                    dense
                  />
                  <q-checkbox
                    v-model="props.row.replacePrice"
                    label="Заменить"
                    dense
                  />
                </PopupEdit>
              </q-td>

              <q-td
                key="for_place"
                class="text-bold text-red cursor-pointer"
                :props="props"
              >
                {{ props.row.for_place | numberFormatFilter }}
                <PopupEdit
                  v-if="combineTableData"
                  :value.sync="props.row.for_place"
                  type="number"
                  :title="props.row.code_client_name"
                  @add-to-save="addToAddSaveArray(props.row, 'for_place')"
                />
              </q-td>

              <q-td
                key="category_name"
                class="cursor-pointer"
                :props="props"
              >
                {{ props.row.category_id | optionsFilter(categories) }}
                <PopupEdit
                  v-if="combineTableData"
                  :value.sync="props.row.category_id"
                  type="number"
                  :title="props.row.code_client_name"
                  @add-to-save="addToAddSaveArray(props.row, 'category_id')"
                >
                  <SearchSelect
                    v-model="props.row.category_id"
                    label="Категория"
                    :dense="$q.screen.xs || $q.screen.sm"
                    :options="categories"
                  />
                </PopupEdit>
              </q-td>

              <q-td
                key="notation"
                :props="props"
              >
                {{ props.row.notation }}
              </q-td>

            </q-tr>
          </template>
        </Table>
      </q-card-section>

      <q-card-section class="q-pt-none">
        Lorem ipsum dolor sit amet consectetur adipisicing elit. Rerum repellendus sit voluptate voluptas eveniet porro.
        Rerum blanditiis perferendis totam, ea at omnis vel numquam exercitationem aut, natus minima, porro labore.
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import { getUrl } from 'src/tools/url';

export default {
  name: 'DialogSendSms',
  components: {
    Table: () => import('components/Elements/Table/Table.vue'),
    PopupEdit: () => import('components/PopupEdit.vue'),
    IconBtn: () => import('components/Buttons/IconBtn.vue'),
    BaseBtn: () => import('components/Buttons/BaseBtn.vue'),
    SearchSelect: () => import('components/Elements/SearchSelect.vue'),
  },
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    values: {
      type: Array,
      default: () => ([]),
    },
    fax: {
      type: Object,
      default: () => ({}),
    },
  },
  data() {
    return {
      maximizedToggle: true,
      faxTableData: [],
      faxTableProperties: {
        columns: [
          {
            name: 'code_client_name',
            label: 'Клиент',
            align: 'center',
            field: 'code_client_name',
            sortable: true,
          },
          {
            name: 'place',
            label: this.$t('place'),
            field: 'place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'kg',
            label: this.$t('kg'),
            field: 'kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_kg',
            label: this.$t('forKg'),
            field: 'for_kg',
            align: 'center',
            sortable: true,
          },
          {
            name: 'for_place',
            label: this.$t('forPlace'),
            field: 'for_place',
            align: 'center',
            sortable: true,
          },
          {
            name: 'category_name',
            label: this.$t('category'),
            field: 'category_name',
            align: 'center',
            sortable: true,
          },
          {
            name: 'notation',
            label: this.$t('notation'),
            field: 'notation',
            align: 'center',
            sortable: true,
          },
          {
            name: 'things',
            label: this.$t('things'),
            field: 'things',
            align: 'center',
            sortable: true,
          },
        ],
      },
      faxTableReactiveProperties: {
        selected: [],
        visibleColumns: ['code_client_name', 'place', 'kg', 'for_kg', 'for_place', 'category_name'],
        title: '',
      },
    };
  },
  computed: {
    dialog: {
      set: function set(val) {
        this.$emit('update:show', val);
      },
      get: function get() {
        return this.show;
      },
    },
  },
  methods: {
    sendSms(allData, selected) {
      const list = _.isEmpty(selected) ? allData : selected;
      const codeIds = _.uniq(_.map(list, 'code_client_id'));
      devlog.log('codeIds', codeIds);
      this.$axios.post(getUrl('codesIds'), { ids: codeIds })
        .then(({ data: { codesData } }) => {
          this.sendSmsDialogData = codesData;
          this.showSendSmsDialog = true;
        });
    },
  },
};
</script>
