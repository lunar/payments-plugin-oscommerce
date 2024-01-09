/// <reference types="cypress" />

'use strict';

import { TestMethods } from '../support/test_methods.js';

describe('lunar plugin version log remotely', () => {
    /**
     * Go to backend site admin
     */
    before(() => {
        cy.goToPage(Cypress.env('ENV_ADMIN_URL'));
        TestMethods.loginIntoAdminBackend();
    });

    /** Send log after full test finished. */
    it('log shop & lunar versions remotely', () => {
        TestMethods.logVersions();
    });
}); // describe