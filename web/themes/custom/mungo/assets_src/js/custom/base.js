/**
 * @file
 * General javascript functionality for the site.
 */

(function ($, Drupal) {
  Drupal.behaviors.move_header = {
    attach: function (context, settings) {
      /**
       * Position the site menu when toolbar is active.
       */
      $(document).on('drupalViewportOffsetChange.toolbar', function (event, offsets) {
        console.log(offsets.top);
        $('header.header-fixed').css('top',(offsets.top));
        $('header.header-fixed').css('left',(offsets.left));
      });
    }
  };
  /**
   * When found dymo-text class wrap each word in a span tag.
   */
  Drupal.behaviors.dymo_text = {
    attach: function (context, settings) {
      $('.dymo-text').each(function () {
        var text = $(this).text().split(' ');
        text = '<span>' + text.join('</span><span>') + '</span>';
        $(this).html(text);
      });
    }
  };
})(jQuery, Drupal);
