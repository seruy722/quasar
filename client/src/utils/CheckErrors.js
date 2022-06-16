import validator from 'validator';
import { reactive } from 'vue';

export default function CheckErrorsFunc() {
    const errorsData = reactive({
        errors: {},
    });

    function setErrors(errors) {
        errorsData.errors = errors;
    }

    function checkRequire({ require, value }) {
        return require && !value;
    }

    function checkErrors(data, callback) {
        const errors = {};
        _.forEach(data, (item, key) => {
            devlog.log('CH', key);
            if (checkRequire(item)) {
                errors[key] = [item.requireError];
            } else if (item.rules) {
                _.forEach(item.rules, ({ name, error, options }) => {
                    if (!validator[name](item.value, options)) {
                        errors[key] = [error];
                    }
                });
            }
        });

        if (!_.isEmpty(errors)) {
            setErrors(errors);
        } else if (_.isFunction(callback)) {
            callback(data);
        }
    }

    return {
        errorsData,
        checkErrors,
    };
}
