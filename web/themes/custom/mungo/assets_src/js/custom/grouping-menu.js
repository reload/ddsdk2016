/**
 * @file
 * Event program related JS-library.
 */

(function ($, Drupal) {
  Drupal.behaviors.groupingMenu = {
    attach: function (context, settings) {
      // Find all program headers for toggle.
      $('.grouping-menu__active_title').click(function () {
        console.log(this);
        $(this).parent().toggleClass('is-active');
        $(this).toggleClass('is-active');
      });
    }
  };
})(jQuery, Drupal);
