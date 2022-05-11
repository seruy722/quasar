/* eslint-disable */
const DEV = process.env.DEV;

function log(...args) {
    if (DEV) {
        console.log(...args);
    }
}

function warn(...args) {
    if (DEV) {
        console.warn(...args);
    }
}

function error(...args) {
    if (DEV) {
        console.error(...args);
    }
}

export { log, warn, error };
