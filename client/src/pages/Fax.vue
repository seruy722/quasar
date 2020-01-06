<template>
  <q-page
    data-vue-component-name="Fax"
  >
    <Table
      :table-properties="faxTableProperties"
      :table-data="faxTableData"
      :table-reactive-properties="faxTableReactiveProperties"
      :title="faxName"
    >
      <template v-slot:top-buttons>
        <q-checkbox
          v-model="combineTableData"
          label="Обьеденено"
          dense
        />
        <IconBtn
          color="positive"
          icon="save"
          tooltip="save"
          :disable="!arrayToUpdate.length"
          @iconBtnClick="updateFaxData"
        />

        <IconBtn
          color="positive"
          icon="explicit"
          tooltip="excel"
          @iconBtnClick="exportFaxData(currentFaxItem)"
        />

        <IconBtn
          color="primary"
          icon="update"
          tooltip="update"
          @iconBtnClick="updateAllPriceInFaxData(currentFaxItem.id)"
        />
      </template>

      <template v-slot:inner-body="{props}">
        <q-tr
          :props="props"
          :class="{table__tr_bold_text: props.row.category === 'Бренд', editing: props.row.edit}"
        >
          <q-td auto-width>
            <q-checkbox
              v-model="props.selected"
              dense
            />
          </q-td>
          <q-td
            key="code"
            :props="props"
            class="text-bold"
          >
            {{ props.row.code }}
          </q-td>

          <q-td
            key="code_id"
            :props="props"
            class="cursor-pointer"
          >
            {{ props.row.code_id | filterFromSelectData(clientCodes) }}
            <PopupEdit
              :value.sync="props.row.code_id"
              :edit.sync="props.row.edit"
              title="Клиент"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            >
              <SearchSelect
                v-model="props.row.code_id"
                label="Клиент"
                :options="clientCodes"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="customer_code"
            :props="props"
          >
            {{ props.row.customer_code }}
            <Icon
              v-show="props.row.arr"
              :name="props.expand ? 'arrow_drop_up' : 'arrow_drop_down'"
              :tooltip="props.expand ? 'hide' : 'reveal'"
              class="cursor-pointer"
              @iconClick="props.expand = !props.expand"
            />
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
            :class="{'cursor-pointer': !props.row.arr}"
          >
            {{ props.row.kg }}
            <PopupEdit
              v-if="!props.row.arr"
              :value.sync="props.row.kg"
              :edit.sync="props.row.edit"
              title="Вес"
              type="number"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            />
          </q-td>

          <!--          <q-td-->
          <!--            key="category_id"-->
          <!--            :props="props"-->
          <!--          >-->
          <!--            {{ props.row.category_id }}-->
          <!--          </q-td>-->

          <q-td
            key="for_kg"
            :props="props"
            class="cursor-pointer text-bold text-red"
          >
            {{ props.row.for_kg }}
            <PopupEdit
              :value.sync="props.row.for_kg"
              :edit.sync="props.row.edit"
              type="number"
              title="За кг"
              @addToSave="addToSaveMultiply(props.row.arr, props.row.for_kg, 'for_kg')"
            />
          </q-td>

          <q-td
            key="for_place"
            :props="props"
            class="cursor-pointer text-bold text-red"
          >
            {{ props.row.for_place }}
            <PopupEdit
              :value.sync="props.row.for_place"
              :edit.sync="props.row.edit"
              type="number"
              title="За место"
              @addToSave="addToSaveMultiply(props.row.arr, props.row.for_place, 'for_place')"
            />
          </q-td>

          <q-td
            key="category_id"
            :props="props"
            class="cursor-pointer"
          >
            {{ props.row.category_id | filterFromSelectData(categories) }}
            <PopupEdit
              :value.sync="props.row.category_id"
              :edit.sync="props.row.edit"
              title="Категория"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            >
              <SearchSelect
                v-model="props.row.category_id"
                label="Категория"
                :options="categories"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="sum"
            :props="props"
            class="text-bold"
          >
            {{ props.row.sum | numberFormatFilter}}
          </q-td>

          <q-td
            key="shop_id"
            :props="props"
            class="cursor-pointer"
          >
            {{ props.row.shop_id | filterFromSelectData(shopsList) }}
            <PopupEdit
              :value.sync="props.row.shop_id"
              :edit.sync="props.row.edit"
              title="Магазин"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            >
              <SearchSelect
                v-model="props.row.shop_id"
                label="Магазин"
                :options="shopsList"
              />
            </PopupEdit>
          </q-td>

          <q-td
            key="notation"
            :props="props"
            class="cursor-pointer"
          >
            {{ props.row.notation }}
            <PopupEdit
              :value.sync="props.row.notation"
              :edit.sync="props.row.edit"
              title="Примечания"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            />
          </q-td>

          <q-td
            key="things"
            :props="props"
            class="cursor-pointer"
            @click="props.row.dialog = true"
          >
            <DialogChangeThings
              :things-data="props.row"
              title="Опись вложения"
              @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
            />
            {{ props.row.things | jsonDecode }}
          </q-td>
        </q-tr>
        <q-tr
          v-show="props.expand"
          :props="props"
        >
          <q-td colspan="100%">
            <Table
              :table-properties="faxExpandDataTable"
              :table-data="props.row.arr"
            >
              <template v-slot:inner-body="{props}">
                <q-tr
                  :props="props"
                  :class="{table__tr_bold_text: props.row.category === 'Бренд'}"
                >
                  <q-td auto-width>
                    <q-checkbox
                      v-model="props.selected"
                      :dense="$q.screen.xs || $q.screen.sm"
                    />
                  </q-td>

                  <q-td
                    key="code"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.code }}
                    <PopupEdit
                      :value.sync="props.row.code"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="code_id"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.code_id | filterFromSelectData(clientCodes) }}
                    <PopupEdit
                      :value.sync="props.row.code_id"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    >
                      <template v-slot:body>
                        <SelectWithSearchInput
                          :value.sync="props.row.code_id"
                          :options="clientCodes"
                          :search-options="clientCodes"
                        />
                      </template>
                    </PopupEdit>
                  </q-td>

                  <q-td
                    key="place"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.place }}
                    <PopupEdit
                      :value.sync="props.row.place"
                      type="number"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="kg"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.kg }}
                    <PopupEdit
                      :value.sync="props.row.kg"
                      type="number"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="for_kg"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.for_kg }}
                    <PopupEdit
                      :value.sync="props.row.for_kg"
                      type="number"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="for_place"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.for_place }}
                    <PopupEdit
                      :value.sync="props.row.for_place"
                      type="number"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="category_id"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.category_id | filterFromSelectData(categories) }}
                    <PopupEdit
                      :value.sync="props.row.category_id"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    >
                      <template v-slot:body>
                        <SelectWithSearchInput
                          :value.sync="props.row.category_id"
                          :options="categories"
                          :search-options="categories"
                        />
                      </template>
                    </PopupEdit>
                  </q-td>

                  <q-td
                    key="shop_id"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.shop_id }}
                    <PopupEdit
                      :value.sync="props.row.shop_id"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="notation"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.notation }}
                    <PopupEdit
                      :value.sync="props.row.notation"
                      @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                    />
                  </q-td>

                  <q-td
                    key="things"
                    :props="props"
                    class="cursor-pointer"
                  >
                    {{ props.row.things }}
                    <PopupEdit
                      :value.sync="props.row.things"
                    />
                  </q-td>
                </q-tr>
              </template>
            </Table>
          </q-td>
        </q-tr>
      </template>
    </Table>
    <!--СУММЫ ПО ТАБЛИЦЕ-->
    <div class="text-center">
      Мест: <span class="text-bold">{{ formatNumber(countSumObjects(faxTableData, 'place')) }}</span>
      Вес: <span class="text-bold">{{ formatNumber(countSumObjects(faxTableData, 'kg')) }}</span>
      Сумма: <span class="text-bold">{{ formatNumber(roundNumber(countSumObjects(faxTableData, 'sum'), 0)) }}</span>
    </div>
    <!--КАТЕГОРИИ-->
    <div style="max-width: 1000px;margin: 0 auto">
      <div class="text-center text-bold text-uppercase q-mt-lg">Категории</div>
      <Table
        :table-properties="faxCategoriesTable"
        :table-data="faxCategoriesDataList"
      >
        <template v-slot:inner-body="{props}">
          <q-tr
            :props="props"
            :class="{'text-bold': props.row.type}"
          >
            <q-td
              key="name"
              :props="props"
            >
              {{ props.row.name }}
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
              {{ props.row.kg }}
            </q-td>

            <q-td
              key="category_price"
              :props="props"
              class="cursor-pointer text-bold text-red"
            >
              {{ props.row.category_price }}
              <PopupEdit
                v-if="!props.row.type"
                :value.sync="props.row.category_price"
                type="number"
                @addToSave="addToUpdateArray(props.row, 'arrayToUpdateTransporterPriceData')"
              />
            </q-td>

            <q-td
              key="sum"
              :props="props"
              :class="{bg_sum: props.row.type === 'categoriesSumObj' || props.row.type === 'faxDataListSumObj', 'bg_difference_sum': props.row.type === 'differenceSumObj'}"
            >
              {{ props.row.sum | numberFormatFilter}}
            </q-td>
          </q-tr>
          <q-tr
            v-show="props.expand"
            :props="props"
          >
            <q-td colspan="100%">
              <Table
                :table-properties="faxExpandDataTable"
                :table-data="props.row.arr"
              >
                <template v-slot:inner-body="{props}">
                  <q-tr :props="props">
                    <q-td auto-width>
                      <q-checkbox
                        v-model="props.selected"
                        :dense="$q.screen.xs || $q.screen.sm"
                      />
                    </q-td>

                    <q-td
                      key="code"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.code }}
                      <PopupEdit
                        :value.sync="props.row.code"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="code_id"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.code_id | filterFromSelectData(clientCodes) }}
                      <PopupEdit
                        :value.sync="props.row.code_id"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      >
                        <template v-slot:body>
                          <SelectWithSearchInput
                            :value.sync="props.row.code_id"
                            :options="clientCodes"
                            :search-options="clientCodes"
                          />
                        </template>
                      </PopupEdit>
                    </q-td>

                    <q-td
                      key="place"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.place }}
                      <PopupEdit
                        :value.sync="props.row.place"
                        type="number"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="kg"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.kg }}
                      <PopupEdit
                        :value.sync="props.row.kg"
                        type="number"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="for_kg"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.for_kg }}
                      <PopupEdit
                        :value.sync="props.row.for_kg"
                        type="number"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="for_place"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.for_place }}
                      <PopupEdit
                        :value.sync="props.row.for_place"
                        type="number"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="category_id"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.category_id | filterFromSelectData(categories) }}
                      <PopupEdit
                        :value.sync="props.row.category_id"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      >
                        <template v-slot:body>
                          <SelectWithSearchInput
                            :value.sync="props.row.category_id"
                            :options.sync="categories"
                            :search-options="categories"
                          />
                        </template>
                      </PopupEdit>
                    </q-td>

                    <q-td
                      key="shop_id"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.shop_id }}
                      <PopupEdit
                        :value.sync="props.row.shop_id"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>

                    <q-td
                      key="notation"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.notation }}
                      <PopupEdit
                        :value.sync="props.row.notation"
                        @addToSave="addToUpdateArray(props.row, 'arrayToUpdate')"
                      />
                    </q-td>


                    <q-td
                      key="things"
                      :props="props"
                      class="cursor-pointer"
                    >
                      {{ props.row.things }}
                      <PopupEdit
                        :value.sync="props.row.things"
                      />
                    </q-td>
                  </q-tr>
                  <!--                                <q-tr v-show="props.expand" :props="props">-->
                  <!--                                    <q-td colspan="100%">-->
                  <!--                                    </q-td>-->
                  <!--                                </q-tr>-->
                </template>

              </Table>
            </q-td>
          </q-tr>
        </template>
        <template v-slot:inner-bottom-row>
          <IconBtn
            v-if="arrayToUpdateTransporterPriceData.length"
            :icon-btn-data="{
                                color: 'positive',
                                icon: 'save',
                                tooltip: 'save'
                            }"
            @iconBtnClick="updateTransporterFaxesPrice"
          />
        </template>
      </Table>
    </div>
  </q-page>
</template>

<script>
    import { getUrl } from 'src/tools/url';
    import { mapGetters } from 'vuex';
    import showNotif from 'src/mixins/showNotif';
    import ExportDataMixin from 'src/mixins/ExportData';
    import FaxesMixin from 'src/mixins/Faxes';
    import { sortCollection, sortArrayCollection } from 'src/utils/sort';

    export default {
        name: 'Fax',
        components: {
            Table: () => import('src/components/Elements/Table/Table.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            IconBtn: () => import('src/components/Buttons/IconBtn.vue'),
            PopupEdit: () => import('src/components/PopupEdit.vue'),
            SelectWithSearchInput: () => import('src/components/Elements/SelectWithSearchInput.vue'),
            SearchSelect: () => import('src/components/Elements/SearchSelect.vue'),
            DialogChangeThings: () => import('src/components/Dialogs/DialogChangeThings.vue'),
        },
        filters: {
            jsonDecode(val) {
                try {
                    return _.join(_.map(JSON.parse(val), ({ label, value }) => `${label}:${value}`), '; ');
                } catch (e) {
                    devlog.log(`Невернй формат JSON - ${val}`);
                }
                return val;
            },
        },
        mixins: [showNotif, ExportDataMixin, FaxesMixin],
        data() {
            this.combineFaxTableProperties = {
                columns: [
                    {
                        name: 'customer_code',
                        label: this.$t('customer'),
                        align: 'center',
                        field: 'customer_code',
                        sortable: true,
                        sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
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
                        name: 'category_id',
                        label: this.$t('category'),
                        field: 'category_id',
                        align: 'center',
                        sortable: true,
                    },
                    {
                        name: 'sum',
                        label: this.$t('sum'),
                        field: 'sum',
                        align: 'center',
                        sortable: true,
                    },
                ],
            };
            this.generalFaxTableProperties = {
                columns: [
                    {
                        name: 'code',
                        label: this.$t('code.one'),
                        align: 'center',
                        field: 'code',
                        sortable: true,
                    },
                    {
                        name: 'code_id',
                        label: this.$t('customer'),
                        align: 'center',
                        field: 'code_id',
                        sortable: true,
                        sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
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
                        name: 'category_id',
                        label: this.$t('category'),
                        field: 'category_id',
                        align: 'center',
                        sortable: true,
                    },
                    {
                        name: 'shop_id',
                        label: this.$t('shop'),
                        field: 'shop_id',
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
            };
            this.combinefaxTableReactiveProperties = {
                selected: [],
                visibleColumns: ['customer_code', 'place', 'kg', 'category_id', 'for_kg', 'for_place', 'sum'],
            };
            this.generalFaxTableReactiveProperties = {
                selected: [],
                visibleColumns: ['code', 'code_id', 'place', 'kg', 'category_id', 'for_kg', 'for_place', 'shop_id', 'notation', 'things'],
            };
            return {
                combineTableData: true,
                faxTableData: [],
                faxTableProperties: this.freeze(this.combineFaxTableProperties),
                faxTableReactiveProperties: {
                    selected: [],
                    visibleColumns: ['customer_code', 'place', 'kg', 'for_kg', 'for_place', 'category_id', 'sum'],
                    title: '',
                },
                faxGeneralTableData: [],
                faxExpandDataTable: {
                    title: this.$t('storehouse'),
                    selected: [],
                    columns: [
                        {
                            name: 'code',
                            label: this.$t('code.one'),
                            align: 'center',
                            field: 'code',
                            sortable: true,
                        },
                        {
                            name: 'code_id',
                            label: this.$t('customer'),
                            align: 'center',
                            field: 'code_id',
                            sortable: true,
                            sort: (a, b) => parseInt(a, 10) - parseInt(b, 10),
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
                            name: 'category_id',
                            label: this.$t('category'),
                            field: 'category_id',
                            align: 'center',
                            sortable: true,
                        },
                        {
                            name: 'shop_id',
                            label: this.$t('shop'),
                            field: 'shop_id',
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
                    visibleColumns: ['code', 'code_id', 'place', 'kg', 'for_kg', 'for_place', 'category_id', 'shop_id', 'notation', 'things'],
                },
                faxCategoriesTable: {
                    title: this.$t('storehouse'),
                    hideTop: true,
                    hideBottom: true,
                    selection: 'none',
                    selected: [],
                    columns: [
                        {
                            name: 'name',
                            label: this.$t('category'),
                            align: 'center',
                            field: 'name',
                            sortable: false,
                        },
                        {
                            name: 'place',
                            label: this.$t('place'),
                            field: 'place',
                            align: 'center',
                            sortable: false,
                        },
                        {
                            name: 'kg',
                            label: this.$t('kg'),
                            field: 'kg',
                            align: 'center',
                            sortable: false,
                        },
                        {
                            name: 'category_price',
                            label: this.$t('forKg'),
                            field: 'category_price',
                            align: 'center',
                            sortable: false,
                        },
                        {
                            name: 'sum',
                            label: this.$t('sum'),
                            field: 'sum',
                            align: 'center',
                            sortable: false,
                        },
                    ],
                    visibleColumns: ['name', 'place', 'kg', 'category_price', 'sum'],
                },
                // transporterCategoriesDataList: [],
                faxCategoriesDataList: [],
                arrayToUpdate: [],
                arrayToUpdateTransporterPriceData: [],
            };
        },
        computed: {
            ...mapGetters({
                currentFaxItem: 'faxes/getCurrentFaxItem',
                faxData: 'faxes/getFaxData',
                faxCategoriesData: 'faxes/getFaxCategoriesData',
                transporterPriceData: 'transporter/getTransporterPrice',
            }),
            faxName() {
                return _.get(this.currentFaxItem, 'name');
            },
        },
        watch: {
            faxData(val) {
                this.faxTableData = _.cloneDeep(val);
            },
            faxCategoriesData(val) {
                this.faxCategoriesDataList = _.cloneDeep(val);
            },
            combineTableData(val) {
                if (val) {
                    this.faxTableData = _.cloneDeep(this.faxData);
                    this.faxTableProperties = this.freeze(this.combineFaxTableProperties);
                    this.faxTableReactiveProperties = this.combinefaxTableReactiveProperties;
                } else {
                    this.faxTableData = _.flatten(_.map(this.faxTableData, 'arr'));
                    this.setFaxDataSum(this.faxTableData);
                    this.faxTableProperties = this.freeze(this.generalFaxTableProperties);
                    this.faxTableReactiveProperties = this.generalFaxTableReactiveProperties;
                }
            },
        },
        created() {
            const funcArr = ['getCategories', 'getCustomerCode', 'getThingsList', 'getShopsList'];
            _.forEach(funcArr, (func) => {
                if (_.isFunction(func)) {
                    this[func]();
                }
            });
            this.getFaxData(this.$route.params.id);
            // this.faxTableData = _.cloneDeep(this.faxData);
            this.faxCategoriesDataList = _.cloneDeep(this.faxCategoriesData);
            this.faxTableReactiveProperties.title = _.get(this.currentFaxItem, 'name');
        },
        methods: {
            async updateFaxData() {
                this.$q.loading.show();
                await this.$axios.post(getUrl('updateFaxData'), this.arrayToUpdate)
                  .then(async ({ data }) => {
                      devlog.log('SDF', data);
                      await this.$store.dispatch('faxes/setFaxData', sortArrayCollection(this.prepareFaxTableData(data.faxData), 'customer_code'));
                      await this.$store.dispatch('faxes/setFaxCategoriesData', this.categoriesSumData(this.updateCategories(this.faxCategoriesDataList)));
                      this.arrayToUpdate = [];
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log('EDR', errors);
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            async updateTransporterFaxesPrice() {
                this.$q.loading.show();
                await this.$axios.post(getUrl('updateTransporterFaxesPrice'), {
                    transporter_id: this.currentFaxItem.transporter_id,
                    data: this.arrayToUpdateTransporterPriceData,
                })
                  .then(() => {
                      this.$store.dispatch('faxes/setFaxCategoriesData', this.categoriesSumData(this.updateCategories(this.faxCategoriesDataList)));
                      this.arrayToUpdateTransporterPriceData = [];
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch((errors) => {
                      this.$q.loading.hide();
                      devlog.log('EDR', errors);
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            async getFaxData(id) {
                // if (this.currentFaxItem.id !== id) {
                this.$q.loading.show();
                try {
                    const { data: { faxData, fax, transporterPriceData } } = await this.$axios.get(`${getUrl('faxData')}/${id}`);
                    this.$store.dispatch('faxes/setCurrentFaxItem', fax);
                    this.$store.dispatch('faxes/setFaxData', sortArrayCollection(this.prepareFaxTableData(faxData), 'customer_code'))
                      .then(() => {
                          this.$store.dispatch('faxes/setFaxCategoriesData', this.categoriesSumData(sortCollection(this.categoriesPreparation(this.faxTableData, transporterPriceData), ['place'], ['desc'])));
                      });
                    const dat = await Promise.all([this.$store.dispatch('faxes/setCurrentFaxItem', fax)]);
                    devlog.log('DAT', dat);
                    this.$q.loading.hide();
                } catch (e) {
                    devlog.log(e);
                    this.$q.loading.hide();
                }
                // }
            },
            async updateAllPriceInFaxData(id) {
                this.$q.loading.show();
                await this.$axios.get(`${getUrl('updateAllPriceInFaxData')}/${id}`)
                  .then(({ data }) => {
                      this.$store.dispatch('faxes/setFaxData', this.prepareFaxTableData(data.faxData));
                      this.$q.loading.hide();
                      this.showNotif('success', 'Данные успешно обновлены.', 'center');
                  })
                  .catch(() => {
                      this.$q.loading.hide();
                      this.showNotif('error', 'Произошла ошибка. Обновите пожалуйста страницу.', 'center');
                  });
            },
            addToSaveMultiply(arr, price, key) {
                _.forEach(arr, (item) => {
                    item[key] = price;
                    this.addToUpdateArray(item, 'arrayToUpdate');
                });
            },
            exportFaxData({ id, name, transporter: { id: transporterID } }) {
                this.exportDataToExcel(getUrl('exportFaxData'), {
                    faxID: id,
                    transporterID,
                }, `${name}.xlsx`);
            },
        },
    };
</script>

<style lang="stylus" scoped>
  .bg_sum
    background-color: $yellow_bg

  .bg_difference_sum
    background-color: $green
</style>
