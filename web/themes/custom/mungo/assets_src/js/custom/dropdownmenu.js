/**
 * @file
 * Handle dropdown specific click on first menu level.
 */

(function ($, Drupal) {
  Drupal.behaviors.dropdownmenu = {
    attach: function (context, settings) {

      $('ul.menu--main .menu-item--expanded', context).on('click', function () {
        $(this).toggleClass('opened');
      }).children('a').on('click', function (e) {
        // Disable click for any immediate A-tag children, which only can be the
        // first level.
        e.preventDefault();
      });
    }
  };
})(jQuery, Drupal);
