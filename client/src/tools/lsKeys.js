import _ from 'lodash';

const keys = {
    lang: 'locale',
    authToken: 'token',
    moderDrawerItem: 'moderActiveMenuItem',
};

export const getLSKey = (value => _.get(keys, value));
