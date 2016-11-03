/**
 * @file
 * Handle views exposed filter.
 */

(function ($, Drupal) {
  Drupal.behaviors.mungoViewsExposedFilters = {
    attach: function (context, settings) {
      $("#edit-event-before").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
      });

      $("#edit-event-after").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd/mm/yy'
      });
    }
  };
})(jQuery, Drupal);
