const { DEV = false } = process.env;

/* eslint-disable no-console */
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
