describe('Browsing pages on DDS', () => {
  it('Search', () => {
    cy.drupalStartup();

    // Navigating to the search page, and testing filters.
    cy.get('header').contains('Søg').click();
    cy.get('[name="search_text"]').type('Guide: Sådan pakker du rygsækken{enter}');
    cy.get('[data-drupal-selector="edit-search-text"]').last().should('be.visible').invoke('val').should('eq', 'Guide: Sådan pakker du rygsækken');
    cy.get('[href="/artikel/guide-sadan-pakker-du-rygsaekken"]').first().click();
    cy.contains('Se en mini-guide om at pakke rygsækken her - og nørd videre længere nede i artiklen.').should('be.visible');

    cy.get('header').contains('Søg').click();
    cy.get('[name="search_text"]').type('Mærke{enter}');
    cy.get('.facet-item [for="content-type-download"]').click();
    cy.get('.node-teaser--download--link').should('be.visible');
    cy.get('.node-teaser--download--send-mail').should('be.visible');

    // Testing Event search
    cy.get('header').contains('Kalender').should('be.visible').first().click();
    cy.get('.facet-item label').first().click();
    cy.get('.node--type-event.node--view-mode-search-index').should('be.visible');

    // Mærker
    cy.visit('/maerker');

    cy.get('.views-exposed-form a').contains('Familiespejder').click();
    cy.get('.node--type-badge').contains('Familiespejder').should('be.visible');

    cy.visit('/aktiviteter');
    cy.get('[placeholder="Søg efter aktiviteter"]').type('1-dags kollektiv{enter}');
    cy.get('[href="/aktivitet/1-dags-kollektiv"]').first().click();
    cy.contains('120+ minutter').should('be.visible');
  });
});
