<template>
  <div data-vue-component-name="TransfersListSkeleton">
    <slot>
      <div
        v-if="$q.screen.xs"
        class="q-mt-md"
      >
        <ListItem
          v-for="(n, i) in 8"
          :key="i"
          dense
          class="shadow-1 overflow-hidden q-ma-sm"
          style="border-radius: 30px;border: 1px solid lightgray;"
        >
          <ItemSection avatar>
            <Skeleton type="QCheckbox" />
          </ItemSection>

          <ItemSection>
            <ItemLabel>
              <Skeleton type="text" />
            </ItemLabel>
          </ItemSection>

          <ItemSection side>
            <ItemLabel>
              <Icon
                name="keyboard_arrow_down"
                color="grey-4"
                class="q-mr-sm"
                size="18px"
              />
            </ItemLabel>
          </ItemSection>
        </ListItem>
      </div>
      <div
        v-else
        class="q-pa-md"
      >
        <q-markup-table>
          <thead>
          <TR>
            <th
              class="text-left"
              style="width: 150px"
            >
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
            <th class="text-right">
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
            <th class="text-right">
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
            <th class="text-right">
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
            <th class="text-right">
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
            <th class="text-right">
              <Skeleton
                animation="blink"
                type="text"
              />
            </th>
          </TR>
          </thead>

          <tbody>
          <TR
            v-for="n in 5"
            :key="n"
          >
            <td class="text-left">
              <Skeleton
                animation="blink"
                type="text"
                width="85px"
              />
            </td>
            <td
              v-for="n in 5"
              :key="n"
              class="text-right"
            >
              <Skeleton
                animation="blink"
                type="text"
                :width="`${Math.floor(Math.random() * (85 - 35 + 1)) + 35}px`"
              />
            </td>
          </TR>
          </tbody>
        </q-markup-table>
      </div>
    </slot>
  </div>
</template>

<script>
import ItemSection from 'src/components/Elements/List/ItemSection.vue';
import ItemLabel from 'src/components/Elements/List/ItemLabel.vue';
import ListItem from 'src/components/Elements/List/ListItem.vue';
import Icon from 'src/components/Buttons/Icons/Icon.vue';
import Skeleton from 'src/components/Elements/Skeleton.vue';
import TR from 'src/components/Elements/Table/TR.vue';

export default {
  name: 'TransfersListSkeleton',
  components: {
    ItemSection,
    ItemLabel,
    ListItem,
    Icon,
    Skeleton,
    TR,
  },
};
</script>
