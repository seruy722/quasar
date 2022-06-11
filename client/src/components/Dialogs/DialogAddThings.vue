<template>
  <DialogComponent
      v-model:dialog="show"
      :persistent="true"
      title="Опись вложения"
      data-vue-component-name="DialogAddThings"
  >
    <Card style="min-width: 320px;width: 100%;max-width: 900px;">
      <CardSection class="row justify-between bg-grey q-mb-sm">
        <span class="text-h6">Опись вложения</span>
        <div>
          <IconBtn
              dense
              icon="clear"
              tooltip="Закрыть"
              @icon-btn-click="close(thing)"
          />
        </div>
      </CardSection>
      <CardSection>
        <div class="fit row wrap justify-start items-center content-start bg-white">
          <div
              v-for="(item, index) in thing"
              :key="index"
              class="col-xs-12 col-sm-4 col-md-4 col-lg-4 q-pt-md q-px-sm"
          >
            <SelectChips
                v-if="item.type === 'selectChips'"
                v-model.trim="item.value"
                :label="item.label"
                :field="index"
                :dense="$q.screen.xs || $q.screen.sm"
                :options="item.options"
                :func-load-data="item.funcLoadData"
                :errors="errorsData"
            />
            <BaseInput
                v-else
                v-model.number="item.value"
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
                @click-outline-btn="checkErrors(thing, add)"
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
                  @icon-btn-click="remove(index)"
              />
            </ItemSection>
          </ListItem>
        </List>
      </CardSection>
      <CardActions>
        <div class="q-px-sm">
          <OutlineBtn
              :label="$t('clear')"
              color="negative"
              icon="clear"
              @click-outline-btn="clear(thing)"
          />
        </div>

        <div class="q-px-sm">
          <OutlineBtn
              :label="$t('save')"
              color="positive"
              icon="save"
              @click-outline-btn="save(thing)"
          />
        </div>
      </CardActions>
    </Card>
  </DialogComponent>
</template>

<script>
import CheckErrorsMixin from 'src/mixins/CheckErrors';
import { setDefaultData, getThingsList } from 'src/utils/FrequentlyCalledFunctions';
import DialogComponent from 'src/components/Dialogs/DialogComponent.vue';
import IconBtn from 'src/components/Buttons/IconBtn.vue';
import OutlineBtn from 'src/components/Buttons/OutlineBtn.vue';
import BaseInput from 'src/components/Elements/BaseInput.vue';
import List from 'src/components/Elements/List/List.vue';
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Card from 'src/components/Elements/Card/Card.vue';
import CardActions from 'src/components/Elements/Card/CardActions.vue';
import CardSection from 'src/components/Elements/Card/CardSection.vue';
import SelectChips from 'src/components/Elements/SelectChips.vue';

export default {
  name: 'DialogAddThings',
  components: {
    DialogComponent,
    IconBtn,
    OutlineBtn,
    BaseInput,
    List,
    ItemSection,
    ListItem,
    Card,
    CardActions,
    CardSection,
    SelectChips,
  },
  mixins: [CheckErrorsMixin],
  props: {
    things: {
      type: String,
      default: null,
    },
    showDialog: {
      type: Boolean,
      default: false,
    },
    changeThings: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['update:things', 'update:changeThings', 'update:showDialog'],
  data() {
    return {
      show: false,
      localThings: [],
      thing: {
        title: {
          type: 'selectChips',
          label: 'Название',
          rules: [
            {
              name: 'isLength',
              error: 'Минимальное количество символов 2.',
              options: {
                min: 2,
                max: 255,
              },
            },
          ],
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          options: [],
          funcLoadData: getThingsList,
          default: null,
          value: null,
        },
        count: {
          type: 'number',
          label: 'Количество',
          require: true,
          requireError: 'Поле обьзательное для заполнения.',
          default: 0,
          value: 0,
        },
      },
    };
  },
  computed: {
    thingsList() {
      return this.$store.getters['thingsList/getThingsList'];
    },
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
      this.dialog(val);
    },
    show(val) {
      if (val) {
        this.setLocalThings(this.things);
      }
      this.$emit('update:showDialog', val);
    },
    thingsList: {
      handler: function set(val) {
        this.thing.title.options = val;
      },
      immediate: true,
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
    add({
          count: { value: countValue },
          title: { value: titleValue },
        }) {
      devlog.log('ADD', countValue, titleValue);
      this.localThings.unshift({
        count: countValue,
        title: _.startCase(titleValue),
      });
    },
    save(val) {
      if (_.isEmpty(this.localThings)) {
        this.$emit('update:things', null);
      } else {
        this.$emit('update:things', JSON.stringify(this.localThings));
      }
      this.dialog(false);
      this.$emit('update:changeThings', true);
      setDefaultData(val);
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
    close(val) {
      this.show = false;
      setDefaultData(val);
    },
    dialog(val) {
      this.show = val;
    },
  },
};
</script>
