/**
 * @file
 * Handle dropdown specific click on first menu level.
 */

(function ($, Drupal) {
  Drupal.behaviors.dropdownmenu = {
    attach: function (context, settings) {

      $('ul.menu--main .menu-item--expanded', context).on('click', function () {
        // Close the searchbox.
        $('#block-mungo-search input#search-field-expose').prop('checked', false).change();
        // Act on click.
        $(this).siblings().removeClass('opened');
        $(this).toggleClass('opened');
      }).children('a').on('click', function (e) {
        // Disable click for any immediate A-tag children, which only can be the
        // first level.
        e.preventDefault();
      });
      $('#search-field-expose').on('change', function () {
        if ($(this).prop('checked')) {
          $('input.form-search').focus();
          $('ul.menu--main .menu-item--expanded').removeClass('opened');
        }
        else {
          $('input.form-search').blur();
        }
      });
    }
  };
})(jQuery, Drupal);
