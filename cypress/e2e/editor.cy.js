describe('Editor flow', () => {
  it('Create article as editor', () => {
    cy.editorLogin();

    cy.get('[href="/node/add/article"]').contains('Artikel').click();

    const pageTitle = `cypress-test-${Date.now()}`;

    cy.get('[name="title[0][value]"]').type(pageTitle);

    cy.get('#edit-field-content-author-wrapper a').click();
    cy.get('#edit-field-content-author-wrapper input[type="text"]').type('a{enter}');

    cy.get('#edit_field_content_category_chosen a').click();
    cy.get('#edit_field_content_category_chosen input[type="text"]').type('a{enter}');

    cy.get('#edit_field_content_topic_chosen a').click();
    cy.get('#edit_field_content_topic_chosen input[type="text"]').type('a{enter}');

    cy.get('#edit_field_content_target_group_chosen input[type="text"]').type('f{enter}');
    cy.get('[value="Gem"]').click();

  });
})
