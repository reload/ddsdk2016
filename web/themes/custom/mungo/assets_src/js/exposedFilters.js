/**
 * @file
 * Handle views exposed filter.
 */

(function ($, Drupal) {
  Drupal.behaviors.mungoViewsExposedFilters = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      // Toggling the hide/show of hidden filters on mobile.
      $(".js-filters-exposed-toggle-foldout:not(.is-initialized)").on(
        "click",
        function (e) {
          e.preventDefault();
          $(this).addClass("is-initialized");
          $(".js-filters-exposed").toggleClass("is-open");
        },
      );

      $("#edit-event-before").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy",
      });

      $("#edit-event-after").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy",
      });
    },
  };
})(jQuery, Drupal);
