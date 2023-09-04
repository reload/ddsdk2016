const { defineConfig } = require("cypress");

module.exports = defineConfig({
  viewportWidth: 1400,
  videoCompression: false,

  e2e: {
    baseUrl: 'https://ddsdk.docker',
    setupNodeEvents(on, config) {
      // implement node event listeners here
    },
  },
});
