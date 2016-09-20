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
})(jQuery, Drupal);
