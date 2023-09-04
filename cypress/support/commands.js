Cypress.Commands.add('drupalStartup', () => {
  cy.visit('/');

  cy.get('body').then((body) => {
    cy.wait(1000).then(() => {
      if (body.find('#CybotCookiebotDialogBodyLevelButtonLevelOptinAllowAll').length > 0) {
        cy.log('Found cookiebot - will click accept all.');
        cy.get('#CybotCookiebotDialogBodyLevelButtonLevelOptinAllowAll').should('be.visible').click();
        cy.wait(500);
      }
    })
  })
});

Cypress.Commands.add('editorLogin', () => {
  cy.drupalStartup();

  cy.visit('/user/login?destination=/user/edit');

  const password = 'testeditor';

  cy.get('input[data-drupal-selector="edit-name"]').type('testeditor');
  cy.get('input[data-drupal-selector="edit-pass"]').type(`${password}{enter}`);
  cy.get('.page-title').contains('testeditor').should('be.visible');

  cy.get('[href="/admin/content"]').contains('Indhold').should('be.visible').first().click();
  cy.get('.action-links').contains('Tilføj indhold').should('be.visible').first().click();
});
