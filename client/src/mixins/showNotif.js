let notify = () => {
};
export default {
  methods: {
    showNotif(type, message, position, actions) {
      notify();
      switch (type) {
        case 'success':
          notify = this.$q.notify({
            color: 'green',
            textColor: 'white',
            icon: 'thumb_up',
            message,
            position: position || 'top-right',
            multiLine: true,
            actions: actions || [
              {
                label: this.$t('close'),
                color: 'white',
                handler: () => {
                },
              },
            ],
            timeout: 3000,
          });
          break;
        case 'warning':
          notify = this.$q.notify({
            color: 'orange',
            textColor: 'white',
            icon: 'warning',
            message,
            position: position || 'top-right',
            multiLine: true,
            actions: actions || [
              {
                label: this.$t('close'),
                color: 'white',
                handler: () => {
                },
              },
            ],
            timeout: 3000,
          });
          break;
        case 'info':
          notify = this.$q.notify({
            color: 'info',
            textColor: 'white',
            icon: 'info',
            message,
            position: position || 'top-right',
            multiLine: true,
            actions: actions || [
              {
                label: this.$t('close'),
                color: 'white',
                handler: () => {
                },
              },
            ],
            timeout: 3000,
          });
          break;
        case 'error':
          notify = this.$q.notify({
            color: 'red',
            textColor: 'white',
            icon: 'error',
            message,
            position: position || 'top-right',
            multiLine: true,
            actions: actions || [
              {
                label: this.$t('close'),
                color: 'white',
                handler: () => {
                },
              },
            ],
            timeout: 3000,
          });
          break;
        default:
          notify = this.$q.notify(message);
      }
      return notify;
    },
  },
};
