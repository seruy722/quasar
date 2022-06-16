import { Notify } from 'quasar';

let notify = () => {
};
export default function showNotif(type, message, position, actions) {
    notify();
    switch (type) {
        case 'success':
            notify = Notify.create({
                color: 'green',
                textColor: 'white',
                icon: 'thumb_up',
                message,
                position: position || 'top-right',
                actions: actions || [
                    {
                        label: 'Закрыть',
                        color: 'white',
                        handler: () => {
                        },
                    },
                ],
                timeout: 3000,
            });
            break;
        case 'warning':
            notify = Notify.create({
                color: 'orange',
                textColor: 'white',
                icon: 'warning',
                message,
                position: position || 'top-right',
                actions: actions || [
                    {
                        label: 'Закрыть',
                        color: 'white',
                        handler: () => {
                        },
                    },
                ],
                timeout: 3000,
            });
            break;
        case 'info':
            notify = Notify.create({
                color: 'info',
                textColor: 'white',
                icon: 'info',
                message,
                position: position || 'top-right',
                actions: actions || [
                    {
                        label: 'Закрыть',
                        color: 'white',
                        handler: () => {
                        },
                    },
                ],
                timeout: 3000,
            });
            break;
        case 'error':
            notify = Notify.create({
                color: 'red',
                textColor: 'white',
                icon: 'error',
                message,
                position: position || 'top-right',
                actions: actions || [
                    {
                        label: 'Закрыть',
                        color: 'white',
                        handler: () => {
                        },
                    },
                ],
                timeout: 3000,
            });
            break;
        default:
            notify = Notify.create(message);
    }
    return notify;
};
