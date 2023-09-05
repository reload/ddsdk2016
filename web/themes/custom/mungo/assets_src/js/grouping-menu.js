/**
 * @file
 * Event program related JS-library.
 */

(function ($, Drupal) {
  Drupal.behaviors.groupingMenu = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      // Find all program headers for toggle.
      $(".grouping-menu__active_title").click(function () {
        // Toggle a class on the wrapping .grouping-menu element.
        $(this).parent().toggleClass("is-active");
      });
    },
  };
})(jQuery, Drupal);
