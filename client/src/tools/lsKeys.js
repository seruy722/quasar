import { get } from 'lodash';

const keys = {
    lang: 'locale',
    authToken: 'token',
    moderDrawerItem: 'moderActiveMenuItem',
};

export const getLSKey = ((value) => get(keys, value));
