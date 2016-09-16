jQuery(function($){
  /**
   * Position the site menu when toolbar is active.
   */
  $(document).on('drupalViewportOffsetChange.toolbar', function (event, offsets) {
    $('header.header-fixed').offset(offsets);
  });
});
