// ***********************************************************
// This example support/index.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'
import './phoenix_commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')

/**
 * ****************************************************************************
 * *********************** custom code for ocCommerce *************************
 * ****************************************************************************
 *
 * We need this because a third-party JS code has not been loaded correctly,
 * so errors are blocking the testing process.
 *
 * We want to get over some errors, not all of them.
 */
Cypress.on('uncaught:exception', (err, runnable) => {
    /**
     * we expect a 3rd party library error with message 'google is not defined'
     * and don't want to fail the test so we return false
     *
    */
    if (err.message.includes('$ is not defined')) {
      return false
    }
    /** if other libraries don't load, we skip these errors. */
    if (err.message.includes("Cannot read properties of undefined (reading 'substring')")) {
      return false
    }
    /**
     * we still want to ensure there are no other unexpected
     * errors, so we let them fail the test
     */
});