<template>
  <div>
    <RoundBtn
        color="orange"
        tooltip="Статистика"
        icon="trending_up"
        @round-btn-click="show = true"
    />
    <DialogComponent
        :dialog="show"
        :persistent="true"
        :maximized="true"
        transition-show="slide-up"
        transition-hide="slide-down"
        data-vue-component="DialogStatistics"
    >
      <q-card style="max-width: 600px;">
        <q-bar>
          <q-space />
          <IconBtn
              flat
              dense
              icon="close"
              color="negative"
              tooltip="Закрыть"
              @icon-btn-click="show = false"
          />
        </q-bar>

        <q-card-section class="q-pt-none">
          <TransfersStatistics />
        </q-card-section>
      </q-card>
    </DialogComponent>
  </div>
</template>

<script>
import { defineAsyncComponent, ref, defineComponent } from 'vue';

export default defineComponent({
  name: 'DialogStatistics',
  components: {
    DialogComponent: defineAsyncComponent(() => import('src/components/Dialogs/DialogComponent.vue')),
    TransfersStatistics: defineAsyncComponent(() => import('src/components/Transfers/TransfersStatistics.vue')),
    IconBtn: defineAsyncComponent(() => import('src/components/Buttons/IconBtn.vue')),
    RoundBtn: defineAsyncComponent(() => import('src/components/Buttons/RoundBtn.vue')),
  },
  setup() {
    const show = ref(false);
    return {
      show,
    };
  },
});
</script>
