module.exports = {
    env: { browser: true, es2021: true, node: true },
    parserOptions: { ecmaVersion: 12, sourceType: 'module' },
    extends: [
      'plugin:vue/vue3-recommended',
      'plugin:prettier/recommended'
    ],
    plugins: ['vue'],
    rules: {
      'vue/multi-word-component-names': 'off',
      'vue/require-prop-types': 'off',      
      'vue/require-default-prop': 'off',    
      'vue/attributes-order': 'off',        
      'no-console': process.env.NODE_ENV === 'production' ? 'warn' : 'off'
    },
    ignorePatterns: [
      'node_modules/**',
      'public/**',
      'resources/js/bootstrap.js'
    ]
  }