describe('Browsing pages on DDS', () => {
  it('Search', () => {
    cy.drupalStartup();

    // Navigating to the search page, and testing filters.
    cy.get('header').contains('Søg').click();
    cy.get('[name="search_text"]').type('Mærker{enter}');
    cy.get('[data-drupal-selector="edit-search-text"]').last().should('be.visible').invoke('val').should('eq', 'Mærker');
    cy.get('.node--view-mode-teaser').should('exist');

    cy.get('.facet-item [for="content-type-download"]').click();
    cy.get('.node-teaser--download--link').should('be.visible');
    cy.get('.node-teaser--download--send-mail').should('be.visible');

    // Testing Event search
    cy.get('header').contains('Kalender').should('be.visible').first().click();
    cy.get('.facet-item label').first().click();
    cy.get('.node--type-event.node--view-mode-search-index').should('be.visible');
  });

  it('Mærker', () => {
    cy.drupalStartup();
    cy.visit('/maerker');

    cy.get('.views-exposed-form a').contains('Familiespejder').click();
    cy.get('.node--type-badge').contains('Familiespejder').should('be.visible');

  });
});
