/**
 * @file
 * Handle views exposed filter.
 */

(function ($, Drupal) {
  Drupal.behaviors.mungoViewsExposedFilters = {
    attach: function (context, settings) {
      var that = this;

      // Toggling the hide/show of hidden filters on mobile.
      $('.js-filters-exposed-toggle-foldout').once().on('click', function(e) {
        e.preventDefault();

        $('.js-filters-exposed').toggleClass('is-open');
      });

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
