jQuery(function($){
// Update the model when the viewport offset changes.

  $(document).on('drupalViewportOffsetChange.toolbar', function (event, offsets) {
    console.log(offsets);
    $('header.header-fixed').offset(offsets);
  });
});
