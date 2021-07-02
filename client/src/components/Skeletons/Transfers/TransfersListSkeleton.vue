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
    export default {
        name: 'TransfersListSkeleton',
        components: {
            ItemSection: () => import('src/components/Elements/List/ItemSection.vue'),
            ItemLabel: () => import('src/components/Elements/List/ItemLabel.vue'),
            ListItem: () => import('src/components/Elements/List/ListItem.vue'),
            Icon: () => import('src/components/Buttons/Icons/Icon.vue'),
            Skeleton: () => import('src/components/Elements/Skeleton.vue'),
            TR: () => import('src/components/Elements/Table/TR.vue'),
            // TD: () => import('src/components/Elements/Table/TD.vue'),
        },
    };
</script>
