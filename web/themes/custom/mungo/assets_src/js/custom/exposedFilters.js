/**
 * @file
 * Handle views exposed filter.
 */

(function ($, Drupal) {
  Drupal.behaviors.mungoViewsExposedFilters = {
    attach: function (context, settings) {
      $( "#edit-event-before" ).datepicker({
        changeMonth: true,
        changeYear: true
      });

      $( "#edit-event-after" ).datepicker({
        changeMonth: true,
        changeYear: true
      });
    }
  };
})(jQuery, Drupal);
