/**
 * @file
 * Share-icon related JS-library.
 */

(function ($, Drupal) {
  Drupal.behaviors.articleSharePopup = {
    // eslint-disable-next-line no-unused-vars
    attach: function (context, settings) {
      // Handle popups.
      $(".share-popup .popup", context).on({
        click: function (e) {
          console.log(this);
          var w = 500;
          var h = 240;
          var left = (window.innerWidth - w) / 2;
          var top = (window.innerHeight - h) / 3;
          e.preventDefault();
          var href = $(this).attr("href");
          window.open(
            href,
            "Window",
            "toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=" +
              w +
              ", height=" +
              h +
              ", top=" +
              top +
              ", left=" +
              left
          );
        },
      });

      // Handle print.
      $(".share-popup .print", context).on({
        click: function (e) {
          e.preventDefault();
          window.print();
        },
      });
    },
  };
})(jQuery, Drupal);
