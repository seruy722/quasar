module.exports = {
  root: true,

  parserOptions: {
    parser: '@babel/eslint-parser',
    sourceType: 'module',
  },

  env: {
    browser: true,
  },

  // https://github.com/vuejs/eslint-plugin-vue#priority-a-essential-error-prevention
  // consider switching to `plugin:vue/strongly-recommended` or `plugin:vue/recommended` for stricter rules.
  extends: [
    'plugin:vue/vue3-recommended',
    'airbnb-base',
  ],

  // required to lint *.vue files
  plugins: [
    'vue',
  ],

  globals: {
    ga: true, // Google Analytics
    cordova: true,
    __statics: true,
    process: true,
    devlog: true,
    _: true,
  },

  // add your custom rules here
  rules: {
    'max-len': [1, 250, 4],
    'vue/html-indent': ['off', 4],
    indent: [
      'off',
      4,
    ],
    'no-param-reassign': 'off',

    'import/first': 'off',
    'import/named': 'error',
    'import/namespace': 'error',
    'import/default': 'error',
    'import/export': 'error',
    'import/extensions': 'off',
    'import/no-unresolved': 'off',
    'import/no-extraneous-dependencies': 'off',
    'import/prefer-default-export': 'off',
    'prefer-promise-reject-errors': 'off',

    // allow console.log during development only
    'no-console': process.env.NODE_ENV === 'production' ? 'error' : 'off',
    // allow debugger during development only
    'no-debugger': process.env.NODE_ENV === 'production' ? 2 : 0,
  },
};
