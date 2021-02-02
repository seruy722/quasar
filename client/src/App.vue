<template>
  <div id="q-app">
    <router-view />
  </div>
</template>

<script>
import { getUrl } from 'src/tools/url';
/* eslint-disable */
export default {
  name: 'App',
  computed: {
    isUserAuth() {
      return this.$store.getters['auth/isUserAuth'];
    },
  },
  watch: {
    isUserAuth() {
      this.runOnesignal();
    },
  },
  created() {
    this.runOnesignal();
  },
  methods: {
    runOnesignal() {
      console.log('runOnesignal', this.isUserAuth);
      if (this.isUserAuth) {
        OneSignal.push(() => {
          /* These examples are all valid */
          const isPushSupported = OneSignal.isPushNotificationsSupported();
          if (isPushSupported) {
            // Push notifications are supported
            console.log('supported');
            OneSignal.isPushNotificationsEnabled((isEnabled) => {
              if (isEnabled) {
                console.log('Push notifications are enabled!', isEnabled);
                OneSignal.getUserId((userId) => {
                  console.log('OneSignal User ID:', userId);
                  this.$axios.post(getUrl('updatePlayerId'), {
                    player_id: userId,
                  });
                });
              } else {
                console.log('Push notifications are not enabled yet.');
                OneSignal.showNativePrompt();
              }
            });
          } else {
            // Push notifications are not supported
            console.log('not supported');
          }
        });
      }
    },
  },
};
</script>
