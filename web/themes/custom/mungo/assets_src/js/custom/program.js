/**
 * @file
 * Event program related JS-library.
 */

(function ($, Drupal) {
  Drupal.behaviors.mungoProgram = {
    attach: function (context, settings) {
      // Find all programs.
      $('.program').each(function() {
        var program = $(this);
        var programItemsWrapper = $(program).find('.js-program-items');
        var programItems = programItemsWrapper.find('.program-item');

        // Find all program headers for toggle.
        $(program).find('.js-program-header').click(function() {
          $(program).toggleClass('js-active');
          $(programItemsWrapper).toggleClass('js-active');
        });

        // Go through all items and enable toggle of toggle.
        programItems.each(function() {
          var item = $(this);
          var itemContent = item.find('.js-toggle').first();
          var trigger = $(item).find('.js-toggle-trigger');

          trigger.click(function() {
            $(item).toggleClass('js-item-active');
            $(itemContent).toggleClass('js-active');
          });
        });
      });
    }
  };
})(jQuery, Drupal);
